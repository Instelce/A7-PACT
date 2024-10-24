<?php

namespace app\core;

class Storage
{
    /**
     * File are saved in `upload` folder that is in `html` directory.
     * @param string $name
     * @param string $folder
     * @return string Path for the DB
     */
    public function saveFile(string $name, string $folder): string
    {
        // Create the folder if it doesn't exist
        if (!file_exists(Application::$ROOT_DIR . '/html/upload/' . $folder)) {
            mkdir(Application::$ROOT_DIR . '/html/upload/' . $folder, 0777, true);
        }

        $tmpPath = $_FILES[$name]['tmp_name'];
        $extension = explode('.', $_FILES[$name]['name'])[1];

        $fileName = time() . rand(1, 1000) . '.' . $extension;
        $dbPath = '/upload/' . $folder . '/' . $fileName;
        $filePath = Application::$ROOT_DIR . '/html' . $dbPath;

        // Save the file
        move_uploaded_file($tmpPath, $filePath);

        return $dbPath;
    }

    /**
     * Files are saved in `upload` folder that is in `html` directory.
     * @param string $name
     * @param string $folder
     * @return array Paths for the DB
     */
    public function saveFiles(string $name, string $folder): array
    {
        // Create the folder if it doesn't exist
        if (!file_exists(Application::$ROOT_DIR . '/html/upload/' . $folder)) {
            mkdir(Application::$ROOT_DIR . '/html/upload/' . $folder, 0777, true);
        }

        $files = [];

        foreach ($_FILES[$name]['name'] as $i => $fileName) {
            $extension = explode('.', $fileName)[1];
            $tmpPath = $_FILES[$name]['tmp_name'][$i];

            $fileName = time() . rand(1, 1000) . '.' . $extension;
            $dbPath = '/upload/' . $folder . '/' . $fileName;
            $filePath = Application::$ROOT_DIR . '/html' . $dbPath;

            move_uploaded_file($tmpPath, $filePath);

            $files[] = $dbPath;
        }

        return $files;
    }
}