<?php

namespace app\core;

class Storage
{
    /**
     * File are saved in `upload` folder that is in `public` directory.
     * @param string $name
     * @param string $folder
     * @return string Path for the DB
     */
    public function saveFile(string $name, string $folder): string
    {
        $tmpPath = $_FILES[$name]['tmp_name'];
        $extension = explode('.', $_FILES[$name]['name'])[1];

        $fileName = time() . rand(1, 1000) . '.' . $extension;
        $dbPath = '/upload/' . $folder . '/' . $fileName;
        $filePath = Application::$ROOT_DIR . '/public' . $dbPath;

        // Save the file
        move_uploaded_file($tmpPath, $filePath);

        return $dbPath;
    }

    /**
     * Files are saved in `upload` folder that is in `public` directory.
     * @param string $name
     * @param string $folder
     * @return array Paths for the DB
     */
    public function saveFiles(string $name, string $folder): array
    {
        $files = [];

        foreach ($_FILES[$name]['name'] as $i => $fileName) {
            $extension = explode('.', $fileName)[1];
            $tmpPath = $_FILES[$name]['tmp_name'][$i];

            $fileName = time() . rand(1, 1000) . '.' . $extension;
            $dbPath = '/upload/' . $folder . '/' . $fileName;
            $filePath = Application::$ROOT_DIR . '/public' . $dbPath;

            move_uploaded_file($tmpPath, $filePath);

            $files[] = $dbPath;
        }

        return $files;
    }
}