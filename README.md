# Responsive Media Cover Block

The Responsive Media Cover Block plugin extends the core cover block in WordPress to allow for a different image or video for mobile devices. This ensures that your cover media is optimized for both desktop and mobile views.

## Features

- Add a different image or video for mobile devices.
- Seamlessly integrates with the WordPress block editor.
- Easy to use interface for selecting mobile-specific media.
- Falls back to the core cover block if the plugin is disabled.

## Usage

1. Edit or create a new post or page in the WordPress block editor.
2. Add a Cover block to your content.
3. In the block settings, you will see a new "Mobile Media" section.
4. Click on "Select Mobile Media" to upload or choose an image or video specifically for mobile devices.
5. Save your post or page.

### Available Hooks

Filters

- `responsive_media_cover_block_breakpoint`: Change the breakpoint at which the mobile media is displayed. Default `36rem`.
- `responsive_media_cover_block_mobile_image_size`: Change the size used for mobile image. Default `large`.
