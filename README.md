# **Magento 2.0 Wishlist Admin Email Extension** #


## Description ##

The extension sends automatically email notification to the administrator when the customer adds a new product to the personal wish list. 

The email contains the customer name and also the customer email, so the web shop administrator can easily find the user in the admin panel. 

The email also contains the newly added product details. In the extension's configuration section, the administrator is able to set to send only the newly added product details in the notification, or the entire wish list highlighted (marked) with the recently added product.

The extension can be configured only for specified customer groups. The product prices include or exclude tax in the notification email. This depends on proper tax configuration of the store. 

The module's configuration also allows setting custom email address (and separate cc email address) where the notification will be sent. 

The extension comes with a new email template which can be easily loaded and edited at the transactional emails section in the admin panel.

## Features ##

- Module enable / disable.
- Automatically sends an email notification when the customer adds a new product to the wish list.
- Enable customer groups segmentation.
- Can be configured to send the whole wish list or only recently added product.
- Configurable email address and cc email as well.
- Custom email template.
- Multistore support.
- Supported languages: English. 
 
It is a separate module that does not change the default Magento files. 
 
Support: 
Magento Community Edition  2.4.x

## Installation ##

** Important! Always install and test the extension in your development environment, and not on your live or production server. **
 
1. Backup Your Data 
Backup your store database and web directory. 
 
2. Clear Cache and cookies 
Clear the store cache under var/cache and all cookies for your store domain.

3. Upload Files 
Unzip extension contents on your computer and navigate inside the extracted folder. Create folder app / code on your webserver if you don't have it already. Using your FTP client upload content of the directory to your store root / app / code folder.

5. Enable extension
Please use the following commands in the /bin directory of your Magento 2.0 instance:

    php magento module:enable Space_ WishlistAdminEmail

    php magento setup:upgrade 

One more time clear the cache under var/cache and var/page_cache login to Magento backend (admin panel).

## Configuration ##
 
Login to Magento backend (admin panel). You can find the module configuration here: Stores / Configuration, in the left menu Space Extensions / Wishlist Admin Email.

Settings:

** Basic **

Enable Extension: Here you can enable the extension.

Enable Customer Segmentation: Please select yes, if you would like to set specified customer groups for notification.

Enabled Customer Groups (if Customer Segmentation enabled): Please select customer groups.
 
** Email Options **

Email Wish List Items: Please select to send only the recently added item, or the whole wish list. In the second option, the newly added item will be marked.

Send Emails To: Please enter the email address where notification will be sent.
 
Send CC Emails To: Please enter the cc copy email address where notification will be also sent.

Email Sender: Please select the email sender from store email addresses.

Email Template: Please select the custom email template or use the default.

## Change Log ##

Version 1.1.0 - April 1, 2024
- Compatibility with Magento 2.4.x
- Refactor whole code

Version 1.0.2 - January 21, 2018
- Compatibility with Magento 2.2.x

Version 1.0.1 - February 20, 2017
- Composer update

Version 1.0.0 - June 12, 2016
- Compatibility with Magento 2.0.x
- Compatibility with Magento 2.1.x
 
## Support ##
 
If you have any questions about the extension, please contact with me.

## License ##

MIT License.
