WebPage Test  - Akamai Caching Modifications

The WebPageTest modifications included here were used during the 2015 NYC Velocity Presentation  “Advanced Caching Concepts”, by Paul Calvano and Rakesh Chaudhary.   The presentation references http://wpt.advancedcaching.org, which currently redirects here.  Please feel free to reach out to @paulcalvano on Twitter if you have any questions on how to set up and use this. 

In order to use the code here, you need to first set up a WebPageTest private instance.   The latest WebPageTest distribution is located at https://github.com/WPO-Foundation/webpagetest.  Instructions for installing and setting it up are at https://sites.google.com/a/webpagetest.org/docs/private-instances.    The following instructions assume that you have a private agent set up.

The modifications included in this package will add the following features to your WebPageTest private instance:

1. On the homepage, under “Advanced Settings” there will be a “Send Akamai Pragma headers” checkbox.   This defaults to checked
2. On the Test details page (details.php), the request details table will include additional diagnostic information for Akamai requests.   The added feeds include the CPCODE, Cache TTL, an indicator of whether the asset was cacheable, whether it was served from cache and the IP addresses of the Edge and Parent servers.   For non-AKamai requests, if the response contains an X-Cache header, the value will be included in the table.
3. There is an additional report named “Cache Analysis” which is located at cacheanalysis.php.   This report allows you to compare the LastModified header to the Akamai cache TTL and the Cache-Control/Expires header.   A link to the REDbot tool is also provided for further analysis.
4. The download CSV files will include the additional cache details.


Installation Instructions:

1. Back up your existing WebPageTest www folder.
2. Download the modified files in this release.
3. Copy the files into the /var/www/html folder, overwriting the existing WebPageTest files.
4. Run a test and confirm everything is working as expected.




