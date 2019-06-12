# google_contacts-2-yealink_phonebook
A wrapper between Google Contacts/People API and Yealink Remote Phonebook

1. To install .. get your API keys from the Google Console and store it under "tokens" folder under the name "client_secret.json"
2. Run "composer update" to take the Google PHP API vendor files
3. Point your vhost to the "public" folder
4. Run it from CLI like "php public/service.contacts.php" to get your user token (it will be saved into the "tokens" folder)
5. Access it like http(s)://yourdomain.com/service.contacts.php?key=YOUR_SECRET_KEY and you should see it output the Yealink needed XML structure for their remote phonebook

Enjoy!
