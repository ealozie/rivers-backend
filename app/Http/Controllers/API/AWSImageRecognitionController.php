<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Aws\Rekognition\RekognitionClient;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use \CloudConvert\Laravel\Facades\CloudConvert;
use \CloudConvert\Models\Job;
use \CloudConvert\Models\Task;

/**
 * @tags AWS Faceliveness Servce
 */
class AWSImageRecognitionController extends Controller
{
    /**
     * Initiate Faceliveness to obtain Session ID.
     */
    public function initiate_liveness(){
        $rekognition = new RekognitionClient([
            'region'    => env('AWS_DEFAULT_REGION'),
            'version'   => 'latest',
        ]);

         // Start a Face Liveness session
         $result = $rekognition->createFaceLivenessSession([
            'Settings' => [
                'AuditImagesLimit' => 4, // Adjust as needed
                'OutputConfig' => [
                    'S3Bucket' => 'mirt-bucket',
                    'S3KeyPrefix' => 'liveness-tests/', // Adjust as needed
                ],
            ],
        ]);

        // Get the SessionId from the result
        $sessionId = $result['SessionId'];

        return response()->json([
            'status' => 'success',
            'data' => [
                'session_id' => $sessionId
            ]
        ]);
    }

    /**
     * Process Streamed Data.
     */
    public function liveness_results(Request $request){
        $validatedData = $request->validate([
            'sessionid' => 'required'
        ]);

        $sessionId = $validatedData['sessionid'];

        $rekognition = new RekognitionClient([
            'region'    => env('AWS_DEFAULT_REGION'),
            'version'   => 'latest',
        ]);

        $result = $rekognition->getFaceLivenessSessionResults([
            'SessionId' => $sessionId,
        ]);
        // dd($result);
        // Process the session results
        if($result['Confidence'] < 50){
            return response()->json([
                'status' => 'success',
                'data' => $result
            ], 200);
        }

        $s3 = new S3Client([
            'region' => 'us-west-2', // Specify your desired AWS region
            'version' => 'latest',   // Use the latest version of the AWS
        ]);

        $userFaceImageKey = 'liveness-tests/' . $result['SessionId'] . '/reference.jpg';

        $sessionFolders = $s3->listObjects([
            'Bucket' => 'mirt-bucket',
            'Prefix' => 'liveness-tests/',
        ]);

        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $i = 0;
        foreach ($sessionFolders['Contents'] as $sessionFolder) {
            $referenceImageKey = $sessionFolder['Key'];
            $extension = strtolower(pathinfo($referenceImageKey, PATHINFO_EXTENSION));
            $selfFolder = explode('/', $referenceImageKey)[1];
            if($i == 0 || $result['SessionId'] == $selfFolder || $i == count($sessionFolders['Contents']) - 1 || !in_array($extension, $validExtensions)){
                $i++;
                continue;
            }
            $comparisonResult = $rekognition->compareFaces([
                'SimilarityThreshold' => 70, // Adjust as needed
                'SourceImage' => [
                    'S3Object' => [
                        'Bucket' => 'mirt-bucket',
                        'Name' => $userFaceImageKey,
                    ],
                ],
                'TargetImage' => [
                    'S3Object' => [
                        'Bucket' => 'mirt-bucket',
                        'Name' => $referenceImageKey,
                    ],
                ],
            ]);

            // Check the comparison result
            if (!empty($comparisonResult['FaceMatches'])) {
                // User's face matches with a reference face, prevent duplicate sign-up
                return response()->json([
                    'status'=> 'success',
                    'message' => 'You have been captured before.'
                ], 200);
            }
        }
        $storageDirectory = storage_path('app/public/liveness-images/').$sessionId.'/';
        foreach ($result['AuditImages'] as $auditImage) {
            $s3Object = $auditImage['S3Object'];
            $key = $s3Object['Name'];
            $extension = strtolower(pathinfo($key, PATHINFO_EXTENSION));

            // Check if the file extension is valid
            if (!in_array($extension, $validExtensions)) {
                continue; // Skip images without valid extensions
            }

            // Get the image data from S3
            $imageData = Storage::disk('s3')->get($key);

            // Save the image in the storage directory
            $storagePath = $storageDirectory . basename($key);
            if (!is_dir($storageDirectory)) {
                mkdir($storageDirectory, 0775, true);
            }
            file_put_contents($storagePath, $imageData);
        }
        // Allow user to proceed with sign-up
        $user = $request->user();
        $user->unique_id = time() + $user->id + mt_rand(11111, 99999);
        $user->facial_biometric_status = 'completed';
        //$user->facial_biometric_image_url = $imageData; 
        $user->save();
        return response()->json([
            'status' => 'success',
            'data' => $result
        ], 200);

    }
}
