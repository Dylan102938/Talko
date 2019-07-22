# Talko

Learn languages by taking pictures of objects around you!

# To use

01) Clone or download repository  
02) Install WAMP or another Apache server with PHP and MySQL
03) Install Python  
04) Create database named "talko" and ensure table is utf-8  
05) Create table "vocab" and include the following columns:  
 - id: int, primary, auto-increment  
 - image: text, collation utf-8-general-ci  
 - text: text, collation utf-8-general-ci  
 - translation: text, collation utf-8-general-ci  
 - language: text, collation utf-8-general-ci  
 - ex: text, collation utf-8-general-ci  
 - tr: text, collation utf-8-general-ci  
06) Set following: talko -> vocab -> operations -> collation -> utf8_general_ci  
07) Run application in browser  
08) Upload photo  
09) Select object from list  
10) Object added to vocab list  

See demo at 
