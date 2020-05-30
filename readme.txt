=== Plugin Name ===
Contributors: ahmedgeek
Tags: encryption, uploads, secure, files, AES, encrypted
Requires at least: 4.5
Tested up to: 5.2
Requires PHP: 5.6
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Encrypt your uploaded files using state-of-the-art encryption standards, and prevent unwanted access to your private data.

== Description ==

WP Encrypted Uploads ueses state-of-the-art AES-128 encryption standard to secure and encrypt uploaded files' contents.

The plugin supports many file types:

* All Image Files.
* All Audio Files.
* All Video Files.
* PDF Files.
* ZIP Files.

You can turn on or off encryption for each file type individually, and you can choose which **Role** can have access to the encrypted files.

The plugin supports very fast encryption for large files, and uses php output streams to serve files which doesn't consume any memory resources from your server.

## Features

* AES-128 encryption for uploaded files.
* Customize access permissions using WP roles.
* Force download browser-viewable files like Images, Videos and PDFs.
* Supports fast encryption for very large files.

The plugin is all about data security, which means there's never a decrypted version of your files stored in accessible location on your server, the plugin uses temporary files to serve    files for download.


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The plugin will automatically create the custom upload directory inside wp-content.
4. The plugin will automatically create 16 byte AES key for encryption.

== Screenshots ==

1. The settings page.

== Changelog ==

= 1.0 =
* Initial release.