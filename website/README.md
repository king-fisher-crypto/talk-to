# Spiriteo


update database : 

```sql
SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

INSERT INTO calltode.domains (id, domain, DATE_ADD, country_id, iso, active, default_lang_id, order_on_generiq_page) VALUES ('', 'talkto_php.local',  '2021-06-13 15:00:00', 1, 'fr', 1, 1, 0);

INSERT INTO calltode.domain_langs (domain_id, lang_id) VALUES (30, 2);
```

# Admin
/website/app/View/Helper/MetronicHelper.php