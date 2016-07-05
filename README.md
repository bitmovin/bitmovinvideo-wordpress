# Bitmovin Wordpress Plugin

![Latest version](https://img.shields.io/badge/latest-1.0.0-green.svg)
![Wordpress 4.2](https://img.shields.io/badge/wordpress-4.2.x-blue.svg)
![Wordpress 4.1](https://img.shields.io/badge/wordpress-4.1.x-blue.svg)
![Wordpress 4.0](https://img.shields.io/badge/wordpress-4.0.x-blue.svg)
![Wordpress 3.9](https://img.shields.io/badge/wordpress-3.9.x-blue.svg)
![Wordpress 3.8](https://img.shields.io/badge/wordpress-3.8.x-blue.svg)
![PHP >= 5.3](https://img.shields.io/badge/php-%3E=5.3-green.svg)

Installation
--------

1. Follow this guide to create a [Bitmovin account](https://bitmovin.com/tutorials/get-started-bitmovin-html5-adaptive-player).
2. Once logged in, go to the **settings of your Bitmovin user account**, as shown in the figure below and get your **API key**.
3. Do not forget to add the domain of your Wordpress website to the allowed domains in the **player overview**.
5. Install the Bitmovin Player Wordpress Plugin through your Wordpress dashboard or upload via FTP and unzip the plugin archive into the `wp-content/plugins` directory.
6. Activate the plugin through the **Plugins** menu in Wordpress.
7. Go to the **Bitmovin Settings** page via in the main admin menu and fill in your API key.
8. Go to the **Bitmovin videos** page via in the main admin menu and add a video with your configuration.
9. Copy the shortcode from the videos page table, which looks like this **[bitmovin_player id='1'/]**, to your post.

Configuration
--------

Once the plugin is installed two new tabs will appear in Wordpress admin panel.

#### Bitmovin

In this section you can add, remove and edit your videos.

#### Bitmovin Settings

In this section you have to set your Bitmovin API key to get the player working on your Wordpress site.
Your API key can be found in the **settings of your Bitmovin user account**, as shown in the figure below.

![APIKey](https://cloudfront-prod.bitmovin.com/wp-content/uploads/2016/04/api-key.png)
