<?php
// Require Google API PHP Client Library
require_once 'vendor/autoload.php'; // Đảm bảo bạn đã cài đặt Google Client Library thông qua Composer

// Hàm chính để xử lý upload file lên Google Drive
function uploadFileToDrive($filePath, $fileName, $mimeType, $folderName = null) {
    // Khởi tạo client
    $client = new Google_Client();
    $client->setApplicationName('Drive File Upload App');
    $client->setScopes(Google_Service_Drive::DRIVE);

    // Đường dẫn đến file credentials.json bạn đã tải về từ Google Cloud Console
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Khởi tạo service
    $service = new Google_Service_Drive($client);

    // Tạo metadata cho file
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $fileName
    ]);

    // Nếu cần tải lên vào thư mục cụ thể
    if ($folderName !== null) {
        // Tìm hoặc tạo thư mục
        $folderId = getFolderIdByName($service, $folderName);
        if ($folderId === null) {
            $folderId = createFolder($service, $folderName);
        }

        $fileMetadata->setParents([$folderId]);
    }

    // Tạo nội dung file từ đường dẫn
    $content = file_get_contents($filePath);

    // Cài đặt các tham số upload
    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $mimeType,
        'uploadType' => 'multipart',
        'fields' => 'id'
    ]);

    return $file->id;
}

// Hàm lấy ID của thư mục theo tên
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

// Hàm tạo thư mục mới
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

// Xử lý form upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $department = $_POST['department'] ?? '';
    $description = $_POST['description'] ?? '';

    // Xử lý file upload
    if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['file-upload']['tmp_name'];
        $fileName = $_FILES['file-upload']['name'];
        $fileType = $_FILES['file-upload']['type'];

        try {
            // Tạo tên thư mục dựa trên phòng ban
            $folderName = $department ? ucfirst($department) . " Department Files" : "General Uploads";

            // Thêm thông tin người dùng vào tên file nếu cần
            $enhancedFileName = date('Y-m-d') . " - " . $name . " - " . $fileName;

            // Upload file lên Drive
            $fileId = uploadFileToDrive($tmpName, $enhancedFileName, $fileType, $folderName);

            // Lưu thông tin metadata vào cơ sở dữ liệu nếu cần
            // saveToDatabase($name, $email, $department, $description, $fileName, $fileId);

            // Trả về phản hồi thành công
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'File đã được tải lên thành công',
                'fileId' => $fileId
            ]);
            exit;
        } catch (Exception $e) {
            // Xử lý lỗi
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi tải file: ' . $e->getMessage()
            ]);
            exit;
        }
    } else {
        // Không có file hoặc có lỗi
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Không có file được chọn hoặc có lỗi khi tải lên'
        ]);
        exit;
    }
}
?>