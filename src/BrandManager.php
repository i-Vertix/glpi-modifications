<?php

/**
 * -------------------------------------------------------------------------
 * UI Branding plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of UI Branding plugin for GLPI.
 *
 * "UI Branding plugin for GLPI" is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * "UI Branding plugin for GLPI" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with "UI Branding plugin for GLPI". If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2025 by i-Vertix/PGUM.
 * @license   GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * @link      https://github.com/i-Vertix/glpi-modifications
 * -------------------------------------------------------------------------
 */

namespace GlpiPlugin\Mod;

use Session;
use Toolbox;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class BrandManager
{

    public const FILES_DIR = GLPI_PLUGIN_DOC_DIR . "/mod";
    public const BACKUP_DIR = self::FILES_DIR . "/backups";
    public const IMAGES_DIR = self::FILES_DIR . "/images";
    public const RESOURCES_DIR = GLPI_ROOT . "/plugins/mod/resources";
    public const IMAGE_RESOURCES = [
        /*
         * Something special about background.jpg:
         * (OBSOLETE - FOUND SOLUTION BELOW)
         * I tried to figure out several ways to not have to copy this beast into the public pics folder
         * but everything I tried did not work out due to the strict routing limitations of GLPI 11.
         * In GLPI 11 every php page load goes through the GLPI index/router and dismisses the request if not logged in
         * This makes a cache-optimized wrapper php script obsolete. It would have provided the .jpg from the webserver restricted files/_plugins directory.
         * There is also available a hook to integrate the plugin into the login page itself (Hooks::DISPLAY_LOGIN) but I did not want to
         * base64 encode the background image due to performance concerns (no browser caching of the (large) background image).
         * Other than that there is nothing I can actually do. Its not the best option to replace/delete from the glpi/public/pics folder, but here we are...
         * glpi/plugins/mod/public could also work out but as glpi does absolutely not want plugins to write in their own directory I keep the glpi/public/pics directory for now
         *
         * (UPDATE - CURRENT SOLUTION)
         * After having a hard time believing there is no such thing as "public" scripts (not logged in) I stumbled upon the GLPI Firewall and the possibility
         * to add no_check (no login) strategies for plugin url patterns!
         * Currently this is implemented in the plugin_init_mod function inside setup.php.
         * I hope this little trick will also be available in future versions lol
         * otherwise I need to roll back to my previous solutions :(
         *
         */
        "background" => [
            "default" => self::RESOURCES_DIR . "/images/background.jpg",
            "current" => self::IMAGES_DIR . "/background.jpg",
//            "active" => GLPI_ROOT . "/public/pics/plugin_mod_background.jpg",
            "accept" => ["jpeg", "jpg"]
        ],
        "favicon" => [
            "default" => self::RESOURCES_DIR . "/images/favicon.ico",
            "current" => self::IMAGES_DIR . "/favicon.ico",
            "active" => GLPI_ROOT . "/public/pics/favicon.ico",
            "backup" => self::BACKUP_DIR . "/favicon.ico",
            "accept" => ["ico"]
        ],
        "logo_s" => [
            "default" => self::RESOURCES_DIR . "/images/logo-G-100.png",
            "current" => self::IMAGES_DIR . "/logo-G-100.png",
            "active" => [
                GLPI_ROOT . "/public/pics/logos/logo-G-100-black.png",
                GLPI_ROOT . "/public/pics/logos/logo-G-100-grey.png",
                GLPI_ROOT . "/public/pics/logos/logo-G-100-white.png",
            ],
            "backup" => [
                self::BACKUP_DIR . "/logo-G-100-black.png",
                self::BACKUP_DIR . "/logo-G-100-grey.png",
                self::BACKUP_DIR . "/logo-G-100-white.png",
            ],
            "accept" => ["png"]
        ],
        "logo_m" => [
            "default" => self::RESOURCES_DIR . "/images/logo-GLPI-100.png",
            "current" => self::IMAGES_DIR . "/logo-GLPI-100.png",
            "active" => [
                GLPI_ROOT . "/public/pics/logos/logo-GLPI-100-black.png",
                GLPI_ROOT . "/public/pics/logos/logo-GLPI-100-grey.png",
                GLPI_ROOT . "/public/pics/logos/logo-GLPI-100-white.png",
            ],
            "backup" => [
                self::BACKUP_DIR . "/logo-GLPI-100-black.png",
                self::BACKUP_DIR . "/logo-GLPI-100-grey.png",
                self::BACKUP_DIR . "/logo-GLPI-100-white.png",
            ],
            "accept" => ["png"]
        ],
        "logo_l" => [
            "default" => self::RESOURCES_DIR . "/images/logo-GLPI-250.png",
            "current" => self::IMAGES_DIR . "/logo-GLPI-250.png",
            "active" => [
                GLPI_ROOT . "/public/pics/logos/logo-GLPI-250-black.png",
                GLPI_ROOT . "/public/pics/logos/logo-GLPI-250-grey.png",
                GLPI_ROOT . "/public/pics/logos/logo-GLPI-250-white.png",
            ],
            "backup" => [
                self::BACKUP_DIR . "/logo-GLPI-250-black.png",
                self::BACKUP_DIR . "/logo-GLPI-250-grey.png",
                self::BACKUP_DIR . "/logo-GLPI-250-white.png",
            ],
            "accept" => ["png"]
        ],
    ];

    /**
     * @return bool
     */
    private static function initModifiers(): bool
    {
        return copy(self::RESOURCES_DIR . "/modifiers.ini", self::FILES_DIR . "/modifiers.ini");
    }

    /**
     * Installs the necessary parts for the plugin.
     *
     * This method creates required directories for files, backups, and images.
     * It also ensures the default image resources are installed and their backups are created.
     * Additionally, it installs title modification resources.
     *
     * @return void
     */
    public function install(): void
    {
        // create directories for files, backup and images
        if (!file_exists(self::FILES_DIR) && !mkdir(self::FILES_DIR, 0644) && !is_dir(self::FILES_DIR)) {
            die(sprintf('Unable to create plugin directory (%s)', self::FILES_DIR));
        }
        if (!file_exists(self::BACKUP_DIR) && !mkdir(self::BACKUP_DIR, 0644) && !is_dir(self::BACKUP_DIR)) {
            die(sprintf('Unable to create plugin directory (%s)', self::BACKUP_DIR));
        }
        if (!file_exists(self::IMAGES_DIR) && !mkdir(self::IMAGES_DIR, 0644) && !is_dir(self::IMAGES_DIR)) {
            die(sprintf('Unable to create plugin directory (%s)', self::IMAGES_DIR));
        }
        Session::addMessageAfterRedirect("🆗 Created plugin directories");

        // handle default images
        foreach (self::IMAGE_RESOURCES as $imageResource => $paths) {
            if (!file_exists($paths["current"]) && !copy($paths["default"], $paths["current"])) {
                die("Unable to install $imageResource resource");
            }
            // create backup
            if (isset($paths["backup"], $paths["active"])) {
                if (is_array($paths["backup"])) {
                    foreach ($paths["backup"] as $backupIndex => $backupPath) {
                        if (!file_exists($backupPath) && !copy($paths["active"][$backupIndex], $backupPath)) {
                            die("Unable to backup $imageResource resource ($backupIndex)");
                        }
                    }
                } else if (!file_exists($paths["backup"]) && !copy($paths["active"], $paths["backup"])) {
                    die("Unable to backup $imageResource resource");
                }
            }
        }
        Session::addMessageAfterRedirect("🆗 Installed image resources and created backups");

        if (!file_exists(self::FILES_DIR . "/modifiers.ini") && !self::initModifiers()) {
            die("Unable to install modifiers");
        }
        Session::addMessageAfterRedirect("🆗 Installed modifiers");
    }

    /**
     * @return void
     */
    public function uninstall(): void
    {
        foreach (array_keys(self::IMAGE_RESOURCES) as $resourceName) {
            $this->restoreResource($resourceName);
        }
        $this->disableLoginPageModifier();
        Session::addMessageAfterRedirect("🆗 Restored backups");
        // delete files
        Toolbox::deleteDir(self::FILES_DIR);
        Session::addMessageAfterRedirect("🆗 Removed resources and backups");
    }

    /**
     * Checks if a backup exists for the specified resource.
     *
     * @param string $resourceName The name of the resource to check for a backup.
     * @return bool True if a backup exists for the specified resource, otherwise false.
     */
    public static function resourceBackupExists(string $resourceName): bool
    {
        if (!isset(self::IMAGE_RESOURCES[$resourceName]["backup"])) return false;
        if (is_array(self::IMAGE_RESOURCES[$resourceName]["backup"])) {
            return file_exists(self::IMAGE_RESOURCES[$resourceName]["backup"][0]);
        } else if (file_exists(self::IMAGE_RESOURCES[$resourceName]["backup"])) {
            return true;
        }
        return false;
    }

    /**
     * Checks if the active resource has been modified.
     *
     * This method determines whether a resource has been altered by verifying the checksum
     * (MD5 hash) of its active file(s) against its backup file(s). If backups do not exist,
     * or if checksum values differ, the resource is considered modified.
     *
     * @param string $resourceName The name of the resource to check.
     *
     * @return bool Returns true if the resource is modified; otherwise, false.
     */
    public static function isActiveResourceModified(string $resourceName): bool
    {
        if (!isset(self::IMAGE_RESOURCES[$resourceName]["active"], self::IMAGE_RESOURCES[$resourceName]["backup"])) return false;

        if (isset(self::IMAGE_RESOURCES[$resourceName]["backup"])) {
            // backups are made - check md5 hash of files
            if (is_array(self::IMAGE_RESOURCES[$resourceName]["active"])) {
                for ($i = 0, $iMax = count(self::IMAGE_RESOURCES[$resourceName]["active"]); $i < $iMax; $i++) {
                    // if active file does not exist resource is not modified
                    if (!file_exists(self::IMAGE_RESOURCES[$resourceName]["active"][$i])) continue;
                    // if backup file does not exist (user deleted backup folder) we say it is modified
                    if (!file_exists(self::IMAGE_RESOURCES[$resourceName]["backup"][$i])) return true;
                    // compare file hashes
                    if (md5_file(self::IMAGE_RESOURCES[$resourceName]["active"][$i]) !== md5_file(self::IMAGE_RESOURCES[$resourceName]["backup"][$i])) return true;
                }
                return false;
            } else {
                if (!file_exists(self::IMAGE_RESOURCES[$resourceName]["active"])) return false;
                if (!file_exists(self::IMAGE_RESOURCES[$resourceName]["backup"])) return true;
                return md5_file(self::IMAGE_RESOURCES[$resourceName]["active"]) !== md5_file(self::IMAGE_RESOURCES[$resourceName]["backup"]);
            }
        } else if (is_array(self::IMAGE_RESOURCES[$resourceName]["active"])) {
            if (!isset(self::IMAGE_RESOURCES[$resourceName]["active"][0])) return false;
            return file_exists(self::IMAGE_RESOURCES[$resourceName]["active"][0]);
        } else return file_exists(self::IMAGE_RESOURCES[$resourceName]["active"]);
    }

    /**
     * Restores the specified resource to its original state.
     *
     * This method restores a resource by copying backup files to their corresponding active files. If backups
     * are not available, the active files are removed instead.
     *
     * @param string $resourceName The name of the resource to restore.
     *
     * @return void
     */
    public function restoreResource(string $resourceName): void
    {
        if (!isset(self::IMAGE_RESOURCES[$resourceName]["active"])) return;
        if (isset(self::IMAGE_RESOURCES[$resourceName]["backup"])) {
            if (is_array(self::IMAGE_RESOURCES[$resourceName]["active"])) {
                // restore multiple files
                for ($i = 0, $iMax = count(self::IMAGE_RESOURCES[$resourceName]["active"]); $i < $iMax; $i++) {
                    // skip if backup does not exist
                    if (!file_exists(self::IMAGE_RESOURCES[$resourceName]["backup"][$i])) continue;
                    copy(self::IMAGE_RESOURCES[$resourceName]["backup"][$i], self::IMAGE_RESOURCES[$resourceName]["active"][$i]);
                }
            } else {
                // restore a single file
                if (!file_exists(self::IMAGE_RESOURCES[$resourceName]["backup"])) return;
                copy(self::IMAGE_RESOURCES[$resourceName]["backup"], self::IMAGE_RESOURCES[$resourceName]["active"]);
            }
        } else if (is_array(self::IMAGE_RESOURCES[$resourceName]["active"])) {
            // files have no backup - remove all active files
            foreach (self::IMAGE_RESOURCES[$resourceName]["active"] as $activeFile) {
                unlink($activeFile);
            }
        } else {
            // file has no backup - remove the active file
            unlink(self::IMAGE_RESOURCES[$resourceName]["active"]);
        }
    }

    /**
     * Applies the current version of the resource to its active location.
     *
     * This method copies the current version of the specified resource to its active file(s),
     * overwriting the existing active resource.
     *
     * @param string $resourceName The name of the resource to apply.
     *
     * @return void
     */
    public function applyResource(string $resourceName): void
    {
        if (!isset(self::IMAGE_RESOURCES[$resourceName]["active"])) return;
        if (is_array(self::IMAGE_RESOURCES[$resourceName]["active"])) {
            foreach (self::IMAGE_RESOURCES[$resourceName]["active"] as $activeFile) {
                copy(self::IMAGE_RESOURCES[$resourceName]["current"], $activeFile);
            }
        } else {
            copy(self::IMAGE_RESOURCES[$resourceName]["current"], self::IMAGE_RESOURCES[$resourceName]["active"]);
        }
    }

    /**
     * Handles the upload of a resource file.
     *
     * This method uploads a file resource to a predefined path associated with the resource name.
     * It validates the file's type, checks for any upload errors, and ensures the file is moved
     * to the correct location if all conditions are met.
     *
     * @param string $resourceName The name of the resource where the file will be uploaded.
     * @param array $file The file data (including 'tmp_name', 'name', 'error', etc.) from the upload.
     * @return bool Returns true if the file was successfully uploaded; otherwise, false.
     */
    public function uploadResource(string $resourceName, array $file): bool
    {
        if (!isset(self::IMAGE_RESOURCES[$resourceName])) return false;
        if (!isset($file["tmp_name"])) return false;
        if (isset($file["error"]) && $file["error"] !== UPLOAD_ERR_OK) {
            Session::addMessageAfterRedirect(sprintf("❌ Upload of file %s failed (file invalid)", $file["name"]));
            return false;
        }

        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        if (!in_array($extension, self::IMAGE_RESOURCES[$resourceName]["accept"], true)) {
            Session::addMessageAfterRedirect(sprintf("❌ Uploaded file %s is invalid (only %s accepted)", $file["name"], implode(", ", self::IMAGE_RESOURCES[$resourceName]["accept"])));
            return false;
        }
        if (!move_uploaded_file($file["tmp_name"], self::IMAGE_RESOURCES[$resourceName]["current"])) {
            Session::addMessageAfterRedirect(sprintf("❌ Upload of file %s failed", $file["name"]));
            return false;
        }
        return true;
    }

    /**
     * Updates the title by saving it to a designated resource file.
     *
     * This method writes the provided title to a file after applying the necessary escape functions
     * to ensure the content is safe for storage.
     *
     * @param string $title The new title to save.
     *
     * @return void This method does not return a value.
     */
    public function changeTitle(string $title): void
    {
        if (!file_exists(self::FILES_DIR . "/modifiers.ini") && !self::initModifiers()) {
            return;
        }
        $ini = parse_ini_file(self::FILES_DIR . "/modifiers.ini");
        if ($ini === false) {
            /** @noinspection PhpArrayIndexImmediatelyRewrittenInspection */
            $ini = ["title" => "GLPI", "login" => "false"];
        }
        $ini["title"] = htmlescape($title);
        $iniString = [];
        foreach ($ini as $key => $value) {
            $iniString[] = "$key=$value";
        }
        file_put_contents(self::FILES_DIR . "/modifiers.ini", implode("\n", $iniString));
    }

    /**
     * Retrieves the current title.
     *
     * This method fetches the title from the designated resource file. If the file
     * is not accessible or its content is unavailable, a default value is returned.
     *
     * @return string Returns the current title of the resource, or a default value if unavailable.
     */
    public static function getCurrentTitle(): string
    {
        if (!file_exists(self::FILES_DIR . "/modifiers.ini") && !self::initModifiers()) {
            return "GLPI";
        }
        $ini = parse_ini_file(self::FILES_DIR . "/modifiers.ini");
        if ($ini === false) return false;
        if (!isset($ini["title"])) {
            self::initModifiers();
            return "GLPI";
        }
        return $ini["title"];
    }

    /**
     * Checks if the login page has been modified by verifying the relevant configuration in the modifiers.ini file.
     *
     * @return bool Returns true if the login page is flagged as modified, or false otherwise.
     */
    public static function isLoginPageModified(): bool
    {
        if (!file_exists(self::FILES_DIR . "/modifiers.ini") && !self::initModifiers()) {
            return false;
        }
        $ini = parse_ini_file(self::FILES_DIR . "/modifiers.ini");
        if ($ini === false) return false;
        if (!isset($ini["login"])) {
            self::initModifiers();
            return false;
        }
        return $ini["login"] === "1";
    }

    /**
     * Applies modifications to the login page by updating the modifiers.ini file.
     * If the file does not exist or cannot be parsed, it initializes the configuration
     * with default values and flags the login page as modified.
     *
     * @return void
     */
    public function applyLoginPageModifier(): void
    {
        if (!file_exists(self::FILES_DIR . "/modifiers.ini") && !self::initModifiers()) {
            return;
        }
        $ini = parse_ini_file(self::FILES_DIR . "/modifiers.ini");
        if ($ini === false) {
            /** @noinspection PhpArrayIndexImmediatelyRewrittenInspection */
            $ini = ["title" => "GLPI", "login" => "1"];
        }
        $ini["login"] = "1";
        $iniString = [];
        foreach ($ini as $key => $value) {
            $iniString[] = "$key=$value";
        }
        file_put_contents(self::FILES_DIR . "/modifiers.ini", implode("\n", $iniString));
    }

    /**
     * Disables the login page modification by updating the related configuration in the modifiers.ini file.
     *
     * @return void
     */
    public function disableLoginPageModifier(): void
    {
        if (!file_exists(self::FILES_DIR . "/modifiers.ini") && !self::initModifiers()) {
            return;
        }
        $ini = parse_ini_file(self::FILES_DIR . "/modifiers.ini");
        if ($ini === false) {
            /** @noinspection PhpArrayIndexImmediatelyRewrittenInspection */
            $ini = ["title" => "GLPI", "login" => "0"];
        }
        $ini["login"] = "0";
        $iniString = [];
        foreach ($ini as $key => $value) {
            $iniString[] = "$key=$value";
        }
        file_put_contents(self::FILES_DIR . "/modifiers.ini", implode("\n", $iniString));
    }

}
