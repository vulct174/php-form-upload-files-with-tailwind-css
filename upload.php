<?php
// Require Google API PHP Client Library
require_once 'vendor/autoload.php'; // Ensure you have installed the Google Client Library via Composer

// Main function to handle file upload to Google Drive
/**
 * @throws Google_Exception
 */
function uploadFileToDrive($filePath, $fileName, $mimeType, $folderName = null): string
{
    // Initialize client
    $client = new Google_Client();
    $client->setApplicationName('Drive File Upload App');
    $client->setScopes((array)Google_Service_Drive::DRIVE);

    // Path to credentials.json downloaded from Google Cloud Console
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Initialize service
    $service = new Google_Service_Drive($client);

    // Create file metadata
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $fileName
    ]);

    // If a specific folder is required
    if ($folderName !== null) {
        // Find or create folder
        $folderId = getFolderIdByName($service, $folderName);
        if ($folderId === null) {
            $folderId = createFolder($service, $folderName);
        }

        $fileMetadata->setParents([$folderId]);
    }

    // Get file content from path
    $content = file_get_contents($filePath);

    // Upload parameters
    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $mimeType,
        'uploadType' => 'multipart',
        'fields' => 'id'
    ]);

    return $file->id;
}

// Function to get folder ID by name
function getFolderIdByName($service, $folderName) {
    $query = "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false";
    $results = $service->files->listFiles([
        'q' => $query,
        'spaces' => 'drive',
        'fields' => 'files(id)'
    ]);

    if (count($results->getFiles()) === 0) {
        return null;
    }

    return $results->getFiles()[0]->getId();
}

// Function to create a new folder
function createFolder($service, $folderName) {
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $folderName,
        'mimeType' => 'application/vnd.google-apps.folder'
    ]);

    $folder = $service->files->create($fileMetadata, [
        'fields' => 'id'
    ]);

    return $folder->getId();
}

// Handle form upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $department = $_POST['department'] ?? '';
    $description = $_POST['description'] ?? '';

    // Handle file upload
    if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['file-upload']['tmp_name'];
        $fileName = $_FILES['file-upload']['name'];
        $fileType = $_FILES['file-upload']['type'];

        try {
            // Create folder name based on department
            $folderName = $department ? ucfirst($department) . " Department Files" : "General Uploads";

            // Enhance file name with user information
            $enhancedFileName = date('Y-m-d') . " - " . $name . " - " . $fileName;

            // Upload file to Drive
            $fileId = uploadFileToDrive($tmpName, $enhancedFileName, $fileType, $folderName);

            // Save file metadata to database if needed
            // saveToDatabase($name, $email, $department, $description, $fileName, $fileId);

            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'File has been successfully uploaded',
                'fileId' => $fileId
            ]);
            exit;
        } catch (Exception $e) {
            // Handle error
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ]);
            exit;
        }
    } else {
        // No file selected or an error occurred
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'No file selected or an error occurred during upload'
        ]);
        exit;
    }
}
?>