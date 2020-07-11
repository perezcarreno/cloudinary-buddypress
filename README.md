# Cloudinary BuddyPress + Youzer Integration

> Snippets of code that can be added to your bp-custom-php file to enable Cloudinary integration with BuddyPress.

## How does this work?

- Step 1: Create a [Cloudinary](https://cloudinary.com/) account (can be a free account to begin).
- Step 2: Create a folder on Cloudinary called "community"
- Step 3: Have your Cloudinary credentials on hand (Cloud name, API Key, API Secret).
- Step 4: Download [Cloudinary's SDK](https://github.com/cloudinary/cloudinary_php) and place it under your child theme's directory, inside a "lib" folder.
  Example: "/themes/mytheme/lib/cloudinary_sdk/"
- Step 5: Copy the code from bp-custom.php and update all appearances of the following variables with your own credentials:
  $pcc_cloudinary_cloud_name = 'XXXXXXXXXXXXX';
    $pcc_cloudinary_api_key = 'XXXXXXXXXXXXX';
  $pcc_cloudinary_api_secret = 'XXXXXXXXXXXXX';
    $pcc_cloudinary_cloud_name = 'XXXXXXXXXXXXX';
- Step 6: Upload bp-custom.php to your plugins directory (if you don't have it already), or add the new code to your existing bp-custom.php file.
- Step 7: You're done! :)

## Credits

- This repository is maintained by [Armando J. Perez-Carreno on GitHub](https://github.com/perezcarreno)
- All snippets are licensed under the MIT License, unless explicitly stated otherwise.
- Logos, names and trademarks are not to be used without the explicit consent of the maintainers or owners of said assets.
- A big thanks to the [Youzer](https://youzer.kainelabs.com/) team for supplying the necessary hooks/filters.
