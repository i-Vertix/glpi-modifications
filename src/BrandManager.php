<?php

/**
 * UI Branding plugin for GLPI 12.
 *
 * @copyright Copyright (C) 2026 by i-Vertix/PGUM.
 * @license GPLv3
 */

namespace GlpiPlugin\Mod;

use Toolbox;
use Symfony\Component\HttpFoundation\File\UploadedFile;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

final class BrandManager
{
    public const FILES_DIR = GLPI_PLUGIN_DOC_DIR . '/mod';
    public const UPLOADS_DIR = self::FILES_DIR . '/uploads';
    private const CONFIG_FILE = self::FILES_DIR . '/settings.ini';

    private const DEFAULT_CONFIG = [
        'title'       => 'GLPI',
        'login'       => '0',
        'theme_logos' => '0',
        'show_logos'  => '0',
        'favicon'     => '0',
    ];

    private const MIME_MAP = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon'],
    ];

    private const RESOURCE_DEFINITIONS = [
        'background' => [
            'filename'       => 'background.jpg',
            'default'        => 'background.jpg',
            'extensions'     => ['jpg', 'jpeg'],
            'mime'           => 'image/jpeg',
            'max_size'       => 15 * 1024 * 1024,
            'min_dimensions' => [1920, 1080],
        ],
        'favicon' => [
            'filename'       => 'favicon.ico',
            'default'        => 'favicon.ico',
            'extensions'     => ['ico'],
            'mime'           => null,
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_s' => [
            'filename'       => 'logo-G-100.png',
            'default'        => 'logo-G-100.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_m' => [
            'filename'       => 'logo-GLPI-100.png',
            'default'        => 'logo-GLPI-100.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_l' => [
            'filename'       => 'logo-GLPI-250.png',
            'default'        => 'logo-GLPI-250.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_s_black' => [
            'filename'       => 'logo-G-100-black.png',
            'default'        => 'logo-G-100-black.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_s_grey' => [
            'filename'       => 'logo-G-100-grey.png',
            'default'        => 'logo-G-100-grey.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_s_white' => [
            'filename'       => 'logo-G-100-white.png',
            'default'        => 'logo-G-100-white.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_m_black' => [
            'filename'       => 'logo-GLPI-100-black.png',
            'default'        => 'logo-GLPI-100-black.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_m_grey' => [
            'filename'       => 'logo-GLPI-100-grey.png',
            'default'        => 'logo-GLPI-100-grey.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_m_white' => [
            'filename'       => 'logo-GLPI-100-white.png',
            'default'        => 'logo-GLPI-100-white.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_l_black' => [
            'filename'       => 'logo-GLPI-250-black.png',
            'default'        => 'logo-GLPI-250-black.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_l_grey' => [
            'filename'       => 'logo-GLPI-250-grey.png',
            'default'        => 'logo-GLPI-250-grey.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
        'logo_l_white' => [
            'filename'       => 'logo-GLPI-250-white.png',
            'default'        => 'logo-GLPI-250-white.png',
            'extensions'     => ['png'],
            'mime'            => 'image/png',
            'max_size'       => 2 * 1024 * 1024,
            'min_dimensions' => null,
        ],
    ];

    public static function getResourceDir(): string
    {
        return rtrim((string) \Plugin::getPhpDir('mod'), '/') . '/resources/images';
    }

    private static function getResourceRootDir(): string
    {
        return rtrim((string) \Plugin::getPhpDir('mod'), '/') . '/resources';
    }

    public static function getImageResources(): array
    {
        $resources = [];

        foreach (self::RESOURCE_DEFINITIONS as $name => $definition) {
            $resources[$name] = [
                'default' => self::getResourceDir() . '/' . $definition['default'],
                'current' => self::UPLOADS_DIR . '/' . $definition['filename'],
                'accept'  => $definition['extensions'],
            ];
        }

        return $resources;
    }

    public static function isValidResourceKey(string $resourceName): bool
    {
        return isset(self::RESOURCE_DEFINITIONS[$resourceName]);
    }

    public static function getCurrentResourceFile(string $resourceName): ?string
    {
        if (!self::isValidResourceKey($resourceName)) {
            return null;
        }

        $resource = self::getImageResources()[$resourceName];

        // Prefer the administrator-uploaded resource. Fall back to the
        // bundled resource so previews and runtime branding remain available
        // even when the plugin data directory has not been initialized yet.
        if (is_file($resource['current']) && is_readable($resource['current'])) {
            return $resource['current'];
        }

        if (is_file($resource['default']) && is_readable($resource['default'])) {
            return $resource['default'];
        }

        return null;
    }

    public static function getResourceVersion(string $resourceName): string
    {
        $file = self::getCurrentResourceFile($resourceName);
        if ($file === null) {
            return '0';
        }

        return (string) ((int) @filemtime($file));
    }

    public static function getResourceFingerprint(string $resourceName): string
    {
        $file = self::getCurrentResourceFile($resourceName);
        if ($file === null) {
            return '0';
        }

        $hash = @hash_file('sha256', $file);
        return is_string($hash) ? $hash : self::getResourceVersion($resourceName);
    }

    /**
     * Return a stable fingerprint for the generated branding stylesheet.
     *
     * The fingerprint changes only when the plugin version, CSS-affecting
     * configuration, or resources referenced by the stylesheet change.
     */
    public static function getBrandingCssFingerprint(): string
    {
        $config = self::readConfigFile();

        $payload = [
            'plugin' => defined('PLUGIN_MOD_VERSION') ? PLUGIN_MOD_VERSION : '0',
            'login' => $config['login'],
            'show_logos' => $config['show_logos'],
            'theme_logos' => $config['theme_logos'],
            'resources' => [],
        ];

        if ($config['show_logos'] === '1') {
            $resourceNames = $config['theme_logos'] === '1'
                ? [
                    'logo_s_black', 'logo_s_white',
                    'logo_m_black', 'logo_m_white',
                    'logo_l_black', 'logo_l_white',
                ]
                : ['logo_s', 'logo_m', 'logo_l'];

            foreach ($resourceNames as $resourceName) {
                $payload['resources'][$resourceName] = self::getResourceFingerprint($resourceName);
            }
        }

        if ($config['login'] === '1') {
            $payload['resources']['background'] = self::getResourceFingerprint('background');
        }

        $encoded = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($encoded)) {
            return '0';
        }

        return substr(hash('sha256', $encoded), 0, 16);
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

    private static function normalizeConfig(array $values): array
    {
        return [
            'title'       => self::sanitizeTitle($values['title'] ?? self::DEFAULT_CONFIG['title']),
            'login'       => self::toFlag($values['login'] ?? '0'),
            'theme_logos' => self::toFlag($values['theme_logos'] ?? '0'),
            'show_logos'  => self::toFlag($values['show_logos'] ?? '0'),
            'favicon'     => self::toFlag($values['favicon'] ?? '0'),
        ];
    }

    private static function sanitizeTitle(mixed $title): string
    {
        $title = trim(strip_tags((string) $title));
        $title = preg_replace('/[\x00-\x1F\x7F]/u', '', $title) ?? '';

        return mb_substr($title, 0, 255);
    }

    private static function toFlag(mixed $value): string
    {
        return (string) $value === '1' ? '1' : '0';
    }

    private static function readConfigFile(): array
    {
        if (!is_file(self::CONFIG_FILE)) {
            return self::DEFAULT_CONFIG;
        }

        $values = parse_ini_file(self::CONFIG_FILE, false, INI_SCANNER_RAW);
        return is_array($values) ? self::normalizeConfig($values) : self::DEFAULT_CONFIG;
    }

    private static function writeConfigFile(array $values): void
    {
        self::ensureDir(self::FILES_DIR);
        $values = self::normalizeConfig($values);

        $content = sprintf(
            "title=\"%s\"\nlogin=\"%s\"\ntheme_logos=\"%s\"\nshow_logos=\"%s\"\nfavicon=\"%s\"\n",
            str_replace('"', '\\"', $values['title']),
            $values['login'],
            $values['theme_logos'],
            $values['show_logos'],
            $values['favicon'],
        );

        $tempFile = tempnam(self::FILES_DIR, 'mod_');
        if ($tempFile === false) {
            throw new \RuntimeException('Unable to create temporary configuration file.');
        }

        try {
            if (file_put_contents($tempFile, $content, LOCK_EX) === false) {
                throw new \RuntimeException('Unable to write plugin configuration.');
            }

            if (!rename($tempFile, self::CONFIG_FILE)) {
                throw new \RuntimeException('Unable to replace plugin configuration.');
            }
        } finally {
            if (is_file($tempFile)) {
                @unlink($tempFile);
            }
        }
    }

    public static function getConfiguration(): array
    {
        return self::readConfigFile();
    }

    public static function getCurrentTitle(): string
    {
        return self::readConfigFile()['title'];
    }

    public static function isLoginPageModified(): bool
    {
        return self::readConfigFile()['login'] === '1';
    }

    public static function isThemeLogosEnabled(): bool
    {
        return self::readConfigFile()['theme_logos'] === '1';
    }

    public static function isAnyLogoModified(): bool
    {
        return self::readConfigFile()['show_logos'] === '1';
    }

    public static function isFaviconEnabled(): bool
    {
        return self::readConfigFile()['favicon'] === '1';
    }

    public function install(): void
    {
        self::ensureDir(self::FILES_DIR);
        self::ensureDir(self::UPLOADS_DIR);

        if (!is_file(self::CONFIG_FILE)) {
            $values = self::DEFAULT_CONFIG;

            $defaultConfig = self::getResourceRootDir() . '/modifiers.ini';
            if (is_file($defaultConfig)) {
                $defaults = parse_ini_file($defaultConfig, false, INI_SCANNER_RAW);
                if (is_array($defaults)) {
                    $values = array_merge($values, $defaults);
                }
            }

            self::writeConfigFile($values);
        }
    }

    public function uninstall(): void
    {
        // The plugin does not alter GLPI core files. Uninstall only removes
        // plugin-owned persistent data.
        if (is_dir(self::FILES_DIR)) {
            Toolbox::deleteDir(self::FILES_DIR);
        }
    }

    public function setConfiguration(array $values): void
    {
        self::writeConfigFile($values);
    }

    public function updateConfiguration(array $values): void
    {
        $current = self::readConfigFile();
        self::writeConfigFile(array_merge($current, $values));

        global $CFG_GLPI;
        $title = self::getCurrentTitle();
        $CFG_GLPI['app_name'] = $title !== '' ? $title : 'GLPI';
    }

    public function uploadResource(string $resourceName, UploadedFile $file): void
    {
        if (!self::isValidResourceKey($resourceName)) {
            throw new \InvalidArgumentException('Invalid resource.');
        }

        if (!$file->isValid()) {
            throw new \RuntimeException('Upload failed.');
        }

        $definition = self::RESOURCE_DEFINITIONS[$resourceName];
        $extension = strtolower((string) pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        if (!in_array($extension, $definition['extensions'], true)) {
            throw new \RuntimeException('Invalid file type.');
        }

        $size = (int) $file->getSize();
        if ($size <= 0 || $size > $definition['max_size']) {
            throw new \RuntimeException('The uploaded file is too large or empty.');
        }

        $path = $file->getPathname();
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $acceptedMime = self::MIME_MAP[$extension] ?? [];
        if ($acceptedMime !== [] && !in_array($mime, $acceptedMime, true)) {
            throw new \RuntimeException('The uploaded file content does not match its extension.');
        }

        $dimensions = $definition['min_dimensions'];
        if ($dimensions !== null) {
            $imageInfo = @getimagesize($path);
            if (!is_array($imageInfo) || $imageInfo[0] < $dimensions[0] || $imageInfo[1] < $dimensions[1]) {
                throw new \RuntimeException(sprintf(
                    'The image dimensions must be at least %1$dx%2$d pixels.',
                    $dimensions[0],
                    $dimensions[1],
                ));
            }
        }

        $destination = self::UPLOADS_DIR . '/' . $definition['filename'];
        self::ensureDir(dirname($destination));

        $file->move(dirname($destination), basename($destination));
    }

    public function setLoginEnabled(bool $enabled): void
    {
        $this->updateConfiguration(['login' => $enabled ? '1' : '0']);
    }

    public function setThemeLogosEnabled(bool $enabled): void
    {
        $this->updateConfiguration(['theme_logos' => $enabled ? '1' : '0']);
    }

    public function setLogosEnabled(bool $enabled): void
    {
        $this->updateConfiguration(['show_logos' => $enabled ? '1' : '0']);
    }

    public function setFaviconEnabled(bool $enabled): void
    {
        $this->updateConfiguration(['favicon' => $enabled ? '1' : '0']);
    }

}
