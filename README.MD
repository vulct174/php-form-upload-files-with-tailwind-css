# PHP Form Upload Files with Tailwind CSS

![Form Screenshot](screenshot.png)

## Overview

This repository contains a modern and responsive file upload application built with PHP and Tailwind CSS that allows users to upload files directly to Google Drive. The application features a clean user interface and a seamless upload experience with drag-and-drop functionality.

## Features

- 🎨 Modern UI built with Tailwind CSS
- 📁 Google Drive API integration for file uploads
- 🔄 Drag and drop file upload functionality
- 📱 Fully responsive design
- 📋 Form validation for user inputs
- 🔒 Secure file handling
- 📊 Organization of files by department
- 🚀 AJAX form submission without page reloads

## Requirements

- PHP 7.4 or higher
- Composer for dependency management
- Google Cloud Project with Drive API enabled
- Web server (Apache, Nginx, etc.)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/vulct174/php-form-upload-files-with-tailwind-css.git
   cd php-form-upload-files-with-tailwind-css
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up Google API credentials:
    - Create a project in [Google Cloud Console](https://console.cloud.google.com/)
    - Enable the Google Drive API
    - Create OAuth 2.0 credentials
    - Download the credentials JSON file and save it as `credentials.json` in the root directory

4. Configure your web server to serve the application.

## Usage

1. Open the application in your web browser.
2. Fill in the required information:
    - Name
    - Email
    - Department (optional)
    - Description of files (optional)
3. Select files by clicking on the upload area or by dragging and dropping files.
4. Click "Tải lên Drive" to upload the files.
5. Wait for the upload to complete and see the success message.

## Configuration

You can modify the following files to customize the application:

- `index.html`: Main form layout and structure
- `upload.php`: Backend processing and Google Drive API integration
- CSS is handled through Tailwind's utility classes

## Project Structure

```
/
├── index.html           # Main HTML file with the form
├── upload.php           # PHP script for processing uploads
├── credentials.json     # Google API credentials (you need to add this)
├── screenshot.png       # Screenshot of the application
├── README.md            # This documentation
└── vendor/              # Composer dependencies
```

## Google Drive Integration

The application uses the Google PHP Client Library to authenticate and upload files to Google Drive. Files are organized in folders based on the department selected by the user.

Key features of the Drive integration:

- Automatic folder creation for each department
- File naming with date and user information
- Proper MIME type handling for different file types
- Error handling for upload failures

## Security Considerations

- The application validates file types and sizes before uploading
- OAuth 2.0 authentication with Google for secure API access
- Server-side validation of all form inputs
- Secure handling of temporary files

## Customization

### Adding More Fields

To add more form fields, update both the HTML form and the PHP processing script:

1. Add the new field to `index.html`
2. Update the form handling in `upload.php` to process the new field

### Changing File Organization

To modify how files are organized in Google Drive, edit the folder creation logic in `upload.php`.

## Troubleshooting

### Common Issues

1. **Upload Fails**: Check your `credentials.json` file and ensure your Google API project has the Drive API enabled.

2. **Permission Errors**: Make sure your application has been authorized to access the user's Google Drive.

3. **File Size Limitations**: By default, PHP has upload size limitations. Check your `php.ini` configuration.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- [Tailwind CSS](https://tailwindcss.com/) for the utility-first CSS framework
- [Google Drive API](https://developers.google.com/drive) for the cloud storage integration
- [PHP Google API Client](https://github.com/googleapis/google-api-php-client) for the PHP library

---

Made with ❤️ by [vulct174](https://github.com/vulct174)