UPDATE menu_block_langs 
SET title = CONCAT('Menu Block ', menu_block_id)

UPDATE menu_link_langs 
SET title = CONCAT('Menu Link ', menu_link_id)

UPDATE page_langs 
SET name = CONCAT('Page name ', page_id), 
meta_title = CONCAT('Page meta title ', page_id),
meta_description = CONCAT('Page meta desc ', page_id),
content = 'Lorem ipsum...'
WHERE lang_id != 1