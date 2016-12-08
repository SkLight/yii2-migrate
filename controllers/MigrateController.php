<?php
/**
 * Created by sklight
 * Date: 29.09.16
 * Time: 14:09
 */

namespace sklight\migrate\controllers;

use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator as RII;
use yii\helpers\FileHelper;

class MigrateController extends \yii\console\controllers\MigrateController
{
    // Standard name: m160930_103659_init
    const VERSION_LENGTH = 13;
    const VERSION_START  = 1;

    // Contains: migrationName => fullPath
    protected $pathMap = [];

    /**
     * @inheritdoc
     */
    protected function createMigration($class)
    {
        /** @noinspection PhpIncludeInspection */
        require_once $this->pathMap[$class];
        return new $class(['db' => $this->db]);
    }

    /**
     * @inheritdoc
     */
    protected function getNewMigrations()
    {
        $applied = [];
        $migrations = [];

        foreach ($this->getMigrationHistory(null) as $version => $time) {
            $applied[substr($version, self::VERSION_START, self::VERSION_LENGTH)] = true;
        }

        foreach ($this->pathMap as $file => $fullPath) {
            if (!isset($applied[substr($file, self::VERSION_START, self::VERSION_LENGTH)])) {
                $migrations[] = $file;
            }
        }

        sort($migrations);

        return $migrations;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        $directory = new RDI($this->migrationPath, RDI::FOLLOW_SYMLINKS | RDI::SKIP_DOTS);
        $iterator = new RII($directory, RII::SELF_FIRST);

        /** @var \SplFileInfo $fileInfo */
        foreach ($iterator as $fullPath => $fileInfo) {
            if (!is_file($fullPath)) {
                continue;
            }

            if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $fileInfo->getFilename(), $matches)) {
                $this->pathMap[$matches[1]] = $fullPath;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     */
    public function actionCreate($name)
    {
        $this->migrationPath .= DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');
        FileHelper::createDirectory($this->migrationPath);

        return parent::actionCreate($name);
    }
}
