# GLPI Customization Plugin

## INFO
**GLPI version support starting from 10.0**

For older version support (< 10.0) head over to the original repository.

This repository is a fork
of [https://github.com/stdonato/glpi-modifications](https://github.com/stdonato/glpi-modifications). As the original
owner of this repository does not continue to support future versions of GLPI, we decided to maintain it.
We have rewritten most of the code but the functionalities stay more or less the same.

This plugin and it's maintainers are not connected to the GLPI project in any way.

## INSTALLATION
Download plugin archive from https://github.com/i-Vertix/glpi-modifications/releases for the required GLPI version and unzip the archive to the glpi plugins folder.
After unzipping a new folder called "mod" should appear in your plugins folder. If not, make sure the unzipped folder is located in the glpi plugins folder (glpi/plugins) and is renamed to "mod".
Now log in to glpi and install the GLPI Modifications plugin.

## USAGE
To modify the login background image and icons head over to the plugin settings in the glpi plugin manager. From there you can upload your resources. Please make sure to respect the required image resolutions to ensure proper visualization.
You can also roll back all or single images if you'd like to. the plugin creates a backup for every replaced file.
If you uninstall the plugin or disable it, all backups are restored automatically.

## UNINSTALL
Please uninstall the plugin before removing any plugin files! Otherwise, all backups of original files are lost. We do not take any credit for deleted files!

## FEATURE LIST

- replace GLPI logo of login page and menu with custom logo
- add background to login page
