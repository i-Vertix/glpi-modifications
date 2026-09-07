<?php

/**
 * UI Branding plugin for GLPI 12
 * Copyright (C) 2026 by i-Vertix/PGUM.
 * License GPLv3
 */

namespace GlpiPlugin\Mod;

use Plugin;
use Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Toolbox;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class BrandManager
{
    public const FILES_DIR = GLPI_PLUGIN_DOC_DIR . '/mod';
    public const BACKUP_DIR = self::FILES_DIR . '/backups';
    public const IMAGES_DIR = self::FILES_DIR . '/images';

    private const MIME_MAP = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon'],
    ];

    public static function getResourceDir(): string
    {
        return Plugin::getPhpDir('mod') . '/resources';
    }

    /**
     * Validate a resource key coming from an HTTP request.
     */
    public static function isValidResourceKey(string $resourceName): bool
    {
        return array_key_exists($resourceName, self::getImageResources());
    }

    /**
     * Return the plugin-owned current file for a resource.
     */
    public static function getCurrentResourceFile(string $resourceName): ?string
    {
        $resources = self::getImageResources();
        if (!isset($resources[$resourceName]['current'])) {
            return null;
        }

        $file = $resources[$resourceName]['current'];
        return is_string($file) ? $file : null;
    }

    public static function getImageResources(): array
    {
        $root = GLPI_ROOT . '/public/pics/logos';

        return [
            'background' => [
                'default'  => self::getResourceDir() . '/images/background.jpg',
                'current'  => self::IMAGES_DIR . '/background.jpg',
                'accept'   => ['jpg', 'jpeg'],
            ],
            'favicon' => [
                'default'  => self::getResourceDir() . '/images/favicon.ico',
                'current'  => self::IMAGES_DIR . '/favicon.ico',
                'active'   => GLPI_ROOT . '/public/pics/favicon.ico',
                'backup'   => self::BACKUP_DIR . '/favicon.ico',
                'accept'   => ['ico'],
            ],
            'logo_s' => self::logoGroup(
                self::getResourceDir() . '/images/logo-G-100.png',
                self::IMAGES_DIR . '/logo-G-100.png',
                [
                    $root . '/logo-G-100-black.png',
                    $root . '/logo-G-100-grey.png',
                    $root . '/logo-G-100-white.png',
                ],
                [
                    self::BACKUP_DIR . '/logo-G-100-black.png',
                    self::BACKUP_DIR . '/logo-G-100-grey.png',
                    self::BACKUP_DIR . '/logo-G-100-white.png',
                ]
            ),
            'logo_s_black' => self::logoVariant('logo-G-100.png', 'logo-G-100-black.png'),
            'logo_s_grey'  => self::logoVariant('logo-G-100.png', 'logo-G-100-grey.png'),
            'logo_s_white' => self::logoVariant('logo-G-100.png', 'logo-G-100-white.png'),

            'logo_m' => self::logoGroup(
                self::getResourceDir() . '/images/logo-GLPI-100.png',
                self::IMAGES_DIR . '/logo-GLPI-100.png',
                [
                    $root . '/logo-GLPI-100-black.png',
                    $root . '/logo-GLPI-100-grey.png',
                    $root . '/logo-GLPI-100-white.png',
                ],
                [
                    self::BACKUP_DIR . '/logo-GLPI-100-black.png',
                    self::BACKUP_DIR . '/logo-GLPI-100-grey.png',
                    self::BACKUP_DIR . '/logo-GLPI-100-white.png',
                ]
            ),
            'logo_m_black' => self::logoVariant('logo-GLPI-100.png', 'logo-GLPI-100-black.png'),
            'logo_m_grey'  => self::logoVariant('logo-GLPI-100.png', 'logo-GLPI-100-grey.png'),
            'logo_m_white' => self::logoVariant('logo-GLPI-100.png', 'logo-GLPI-100-white.png'),

            'logo_l' => self::logoGroup(
                self::getResourceDir() . '/images/logo-GLPI-250.png',
                self::IMAGES_DIR . '/logo-GLPI-250.png',
                [
                    $root . '/logo-GLPI-250-black.png',
                    $root . '/logo-GLPI-250-grey.png',
                    $root . '/logo-GLPI-250-white.png',
                ],
                [
                    self::BACKUP_DIR . '/logo-GLPI-250-black.png',
                    self::BACKUP_DIR . '/logo-GLPI-250-grey.png',
                    self::BACKUP_DIR . '/logo-GLPI-250-white.png',
                ]
            ),
            'logo_l_black' => self::logoVariant('logo-GLPI-250.png', 'logo-GLPI-250-black.png'),
            'logo_l_grey'  => self::logoVariant('logo-GLPI-250.png', 'logo-GLPI-250-grey.png'),
            'logo_l_white' => self::logoVariant('logo-GLPI-250.png', 'logo-GLPI-250-white.png'),
        ];
    }

    private static function logoGroup(string $default, string $current, array $active, array $backup): array
    {
        return [
            'default' => $default,
            'current' => $current,
            'active'  => $active,
            'backup'  => $backup,
            'accept'  => ['png'],
        ];
    }

    private static function logoVariant(string $defaultName, string $activeName): array
    {
        $root = GLPI_ROOT . '/public/pics/logos';
        return [
            'default' => self::getResourceDir() . '/images/' . $defaultName,
            'current' => self::IMAGES_DIR . '/' . $activeName,
            'active'  => $root . '/' . $activeName,
            'backup'  => self::BACKUP_DIR . '/' . $activeName,
            'accept'  => ['png'],
        ];
    }

    private static function ensureDir(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Unable to create directory: %s', $dir));
        }
    }

    private static function copyChecked(string $source, string $destination, string $label): void
    {
        if (!is_file($source) || !is_readable($source)) {
            throw new \RuntimeException(sprintf('Source file is not readable: %s', $label));
        }

        $parent = dirname($destination);
        self::ensureDir($parent);

        if (!copy($source, $destination)) {
            throw new \RuntimeException(sprintf('Unable to copy resource: %s', $label));
        }
    }

    private static function removeChecked(string $file): void
    {
        if (!file_exists($file)) {
            return;
        }

        if (!unlink($file)) {
            throw new \RuntimeException(sprintf('Unable to remove resource: %s', $file));
        }
    }

    private static function readModifiers(): array
    {
        $file = self::FILES_DIR . '/modifiers.ini';

        if (!is_file($file)) {
            return [
                'title'       => 'GLPI',
                'login'       => '0',
                'theme_logos' => '0',
            ];
        }

        $ini = parse_ini_file($file, false, INI_SCANNER_RAW);
        if (!is_array($ini)) {
            return [
                'title'       => 'GLPI',
                'login'       => '0',
                'theme_logos' => '0',
            ];
        }

        return [
            'title'       => isset($ini['title']) ? (string) $ini['title'] : 'GLPI',
            'login'       => isset($ini['login']) && (string) $ini['login'] === '1' ? '1' : '0',
            'theme_logos' => isset($ini['theme_logos']) && (string) $ini['theme_logos'] === '1' ? '1' : '0',
        ];
    }

    private static function writeModifiers(array $values): void
    {
        self::ensureDir(self::FILES_DIR);

        $title = str_replace(['"', "\r", "\n"], ['\"', '', ''], strip_tags(trim((string) ($values['title'] ?? 'GLPI'))));
        $login = ($values['login'] ?? '0') === '1' ? '1' : '0';
        $theme = ($values['theme_logos'] ?? '0') === '1' ? '1' : '0';

        $content = sprintf(
            "title=\"%s\"\nlogin=\"%s\"\ntheme_logos=\"%s\"\n",
            $title,
            $login,
            $theme
        );

        $tmp = tempnam(self::FILES_DIR, 'mod_');
        if ($tmp === false || file_put_contents($tmp, $content, LOCK_EX) === false) {
            if ($tmp !== false) {
                @unlink($tmp);
            }
            throw new \RuntimeException('Unable to write plugin configuration.');
        }

        if (!rename($tmp, self::FILES_DIR . '/modifiers.ini')) {
            @unlink($tmp);
            throw new \RuntimeException('Unable to replace plugin configuration.');
        }
    }

    private static function initModifiers(): bool
    {
        try {
            if (is_file(self::getResourceDir() . '/modifiers.ini')) {
                self::copyChecked(
                    self::getResourceDir() . '/modifiers.ini',
                    self::FILES_DIR . '/modifiers.ini',
                    'modifiers.ini'
                );
            } else {
                self::writeModifiers([
                    'title' => 'GLPI',
                    'login' => '0',
                    'theme_logos' => '0',
                ]);
            }
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function install(): void
    {
        self::ensureDir(self::FILES_DIR);
        self::ensureDir(self::BACKUP_DIR);
        self::ensureDir(self::IMAGES_DIR);

        foreach (self::getImageResources() as $name => $resource) {
            if (!is_file($resource['current'])) {
                self::copyChecked($resource['default'], $resource['current'], $name);
            }

            if (!isset($resource['active'], $resource['backup'])) {
                continue;
            }

            if (is_array($resource['active'])) {
                foreach ($resource['active'] as $index => $active) {
                    $backup = $resource['backup'][$index];
                    if (!is_file($backup) && is_file($active)) {
                        self::copyChecked($active, $backup, $name . ' backup');
                    }
                }
            } elseif (!is_file($resource['backup']) && is_file($resource['active'])) {
                self::copyChecked($resource['active'], $resource['backup'], $name . ' backup');
            }
        }

        if (!is_file(self::FILES_DIR . '/modifiers.ini')) {
            if (!self::initModifiers()) {
                throw new \RuntimeException('Unable to initialize plugin configuration.');
            }
        }
    }

    public function uninstall(): void
    {
        foreach (array_keys(self::getImageResources()) as $resource) {
            $this->restoreResource($resource);
        }

        $this->disableLoginPageModifier();

        if (is_dir(self::FILES_DIR)) {
            Toolbox::deleteDir(self::FILES_DIR);
        }
    }

    public static function resourceBackupExists(string $resourceName): bool
    {
        $resources = self::getImageResources();
        if (!isset($resources[$resourceName]['backup'])) {
            return false;
        }

        $backup = $resources[$resourceName]['backup'];
        if (is_array($backup)) {
            foreach ($backup as $file) {
                if (!is_file($file)) {
                    return false;
                }
            }
            return true;
        }

        return is_file($backup);
    }

    public static function isActiveResourceModified(string $resourceName): bool
    {
        $resources = self::getImageResources();
        if (!isset($resources[$resourceName]['active'])) {
            return false;
        }

        $active = $resources[$resourceName]['active'];
        $backup = $resources[$resourceName]['backup'] ?? null;

        if (is_array($active)) {
            foreach ($active as $index => $activeFile) {
                if (!is_file($activeFile)) {
                    continue;
                }
                if (!is_array($backup) || !isset($backup[$index]) || !is_file($backup[$index])) {
                    return true;
                }
                if (md5_file($activeFile) !== md5_file($backup[$index])) {
                    return true;
                }
            }
            return false;
        }

        if (!is_file($active)) {
            return false;
        }
        if (!is_string($backup) || !is_file($backup)) {
            return true;
        }

        return md5_file($active) !== md5_file($backup);
    }

    public static function isAnyLogoModified(): bool
    {
        foreach ([
            'logo_s', 'logo_m', 'logo_l',
            'logo_s_black', 'logo_s_grey', 'logo_s_white',
            'logo_m_black', 'logo_m_grey', 'logo_m_white',
            'logo_l_black', 'logo_l_grey', 'logo_l_white',
        ] as $resource) {
            if (self::isActiveResourceModified($resource)) {
                return true;
            }
        }

        return false;
    }

    public function restoreResource(string $resourceName): void
    {
        $resources = self::getImageResources();
        if (!isset($resources[$resourceName]['active'])) {
            return;
        }

        $active = $resources[$resourceName]['active'];
        $backup = $resources[$resourceName]['backup'] ?? null;

        if (is_array($active)) {
            foreach ($active as $index => $activeFile) {
                if (is_array($backup) && isset($backup[$index]) && is_file($backup[$index])) {
                    self::copyChecked($backup[$index], $activeFile, $resourceName . ' restore');
                }
            }
            return;
        }

        if (is_string($backup) && is_file($backup)) {
            self::copyChecked($backup, $active, $resourceName . ' restore');
            return;
        }

        self::removeChecked($active);
    }

    public function applyResource(string $resourceName): void
    {
        $resources = self::getImageResources();
        if (!isset($resources[$resourceName]['active'])) {
            return;
        }

        $resource = $resources[$resourceName];

        if (!is_file($resource['current']) && is_file($resource['default'])) {
            self::copyChecked($resource['default'], $resource['current'], $resourceName . ' default');
        }

        if (!is_file($resource['current'])) {
            throw new \RuntimeException(sprintf('Current resource is missing: %s', $resourceName));
        }

        $active = $resource['active'];
        if (is_array($active)) {
            foreach ($active as $index => $activeFile) {
                self::copyChecked($resource['current'], $activeFile, $resourceName . ' apply ' . $index);
            }
        } else {
            self::copyChecked($resource['current'], $active, $resourceName . ' apply');
        }
    }

    public function uploadResource(string $resourceName, UploadedFile|array $file): bool
    {
        $resources = self::getImageResources();
        if (!isset($resources[$resourceName])) {
            return false;
        }

        if ($file instanceof UploadedFile) {
            if (!$file->isValid()) {
                Session::addMessageAfterRedirect(__('Upload failed.', 'mod'));
                return false;
            }

            $tmpName = $file->getPathname();
            $originalName = $file->getClientOriginalName();
            $size = (int) $file->getSize();
        } else {
            if (!isset($file['tmp_name']) || !is_string($file['tmp_name'])) {
                return false;
            }
            if (!is_uploaded_file($file['tmp_name'])) {
                Session::addMessageAfterRedirect(__('Invalid uploaded file.', 'mod'));
                return false;
            }

            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                Session::addMessageAfterRedirect(__('Upload failed.', 'mod'));
                return false;
            }

            $tmpName = $file['tmp_name'];
            $originalName = (string) ($file['name'] ?? '');
            $size = (int) ($file['size'] ?? 0);
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, $resources[$resourceName]['accept'], true)) {
            Session::addMessageAfterRedirect(
                sprintf(__('Invalid file type. Allowed: %s', 'mod'), implode(', ', $resources[$resourceName]['accept']))
            );
            return false;
        }

        $maxSize = $resourceName === 'background' ? 15 * 1024 * 1024 : 2 * 1024 * 1024;
        if ($size <= 0 || $size > $maxSize) {
            Session::addMessageAfterRedirect(__('The uploaded file is too large or empty.', 'mod'));
            return false;
        }

        $mime = Toolbox::getMime($tmpName);
        $expected = self::MIME_MAP[$extension] ?? [];
        if (!in_array($mime, $expected, true)) {
            Session::addMessageAfterRedirect(__('The uploaded file content does not match its extension.', 'mod'));
            return false;
        }

        $destination = $resources[$resourceName]['current'];
        self::ensureDir(dirname($destination));

        if ($file instanceof UploadedFile) {
            try {
                $file->move(dirname($destination), basename($destination));
            } catch (\Throwable) {
                Session::addMessageAfterRedirect(__('Unable to store uploaded file.', 'mod'));
                return false;
            }
        } elseif (!move_uploaded_file($tmpName, $destination)) {
            Session::addMessageAfterRedirect(__('Unable to store uploaded file.', 'mod'));
            return false;
        }

        return true;
    }

    public function changeTitle(string $title): void
    {
        $values = self::readModifiers();
        $values['title'] = $title;
        self::writeModifiers($values);
    }

    /**
     * Read the configured title without creating or modifying plugin files.
     */
    public static function getConfiguredTitle(): string
    {
        return self::readModifiers()['title'];
    }

    /**
     * Read login modifier state without creating or modifying plugin files.
     */
    public static function isLoginPageModifierEnabled(): bool
    {
        return self::readModifiers()['login'] === '1';
    }

    public static function getCurrentTitle(): string
    {
        if (!is_file(self::FILES_DIR . '/modifiers.ini') && !self::initModifiers()) {
            return 'GLPI';
        }

        return self::readModifiers()['title'];
    }

    public static function isLoginPageModified(): bool
    {
        if (!is_file(self::FILES_DIR . '/modifiers.ini') && !self::initModifiers()) {
            return false;
        }

        return self::readModifiers()['login'] === '1';
    }

    public static function isThemeLogosEnabled(): bool
    {
        if (!is_file(self::FILES_DIR . '/modifiers.ini') && !self::initModifiers()) {
            return false;
        }

        return self::readModifiers()['theme_logos'] === '1';
    }

    public function setThemeLogosEnabled(bool $enabled): void
    {
        $values = self::readModifiers();
        $values['theme_logos'] = $enabled ? '1' : '0';
        self::writeModifiers($values);
    }

    public function applyLoginPageModifier(): void
    {
        $values = self::readModifiers();
        $values['login'] = '1';
        self::writeModifiers($values);
    }

    public function disableLoginPageModifier(): void
    {
        $values = self::readModifiers();
        $values['login'] = '0';
        self::writeModifiers($values);
    }
}
