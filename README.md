**Magento 2 Module Experius Page Not Found 404**

This module saves all 404 urls to a database table.

- Adds an admin grid with 404s
- It includes a count so you can see which 404 urls needs your attention first.
- Allows you to configure a permanent redirect for the 404s found in the admin grid.

Location in the admin panel: System > Tools > 404 Reports


**Why should you use it?** 

1. Reports all 404s, not only the ones from Google. 
2. Store owner can fix them by them 
3. Import redirect list when migrating to M2.


**How to use the import csv function?**

1. Create a csv called "pagenotfound.csv" with two two columns: from and to url (don't add column headers)
2. Add the full url including https:// (for both from and to url) to this csv
3. Upload csv on the Magento server (f.e. var/import folder)
4. Run the import file command including the url from the previous step 
