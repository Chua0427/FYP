-- Update cart table to allow multiple items per user
-- First check if the unique constraint exists
SELECT COUNT(*) INTO @unique_exists 
FROM information_schema.TABLE_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = DATABASE() 
AND TABLE_NAME = 'cart'
AND CONSTRAINT_NAME = 'unique_user_cart';

-- Drop foreign keys if they exist
SET @fksql1 = (SELECT IF(
    EXISTS(
        SELECT * FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND TABLE_NAME = 'cart'
        AND CONSTRAINT_NAME = 'cart_ibfk_1'
    ),
    'ALTER TABLE cart DROP FOREIGN KEY cart_ibfk_1',
    'SELECT 1'
));
PREPARE stmt FROM @fksql1;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fksql2 = (SELECT IF(
    EXISTS(
        SELECT * FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND TABLE_NAME = 'cart'
        AND CONSTRAINT_NAME = 'cart_ibfk_2'
    ),
    'ALTER TABLE cart DROP FOREIGN KEY cart_ibfk_2',
    'SELECT 1'
));
PREPARE stmt FROM @fksql2;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Drop unique constraint if it exists
SET @sql = (SELECT IF(
    @unique_exists > 0,
    'ALTER TABLE cart DROP INDEX unique_user_cart',
    'SELECT 1'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if composite unique key exists
SELECT COUNT(*) INTO @composite_exists 
FROM information_schema.TABLE_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = DATABASE() 
AND TABLE_NAME = 'cart'
AND CONSTRAINT_NAME = 'unique_cart_item';

-- Add composite unique key if it doesn't exist
SET @sql = (SELECT IF(
    @composite_exists = 0,
    'ALTER TABLE cart ADD CONSTRAINT unique_cart_item UNIQUE (user_id, product_id, product_size)',
    'SELECT 1'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure cart_id is auto increment
SET @sql = 'ALTER TABLE cart MODIFY cart_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add back foreign keys
ALTER TABLE cart
ADD CONSTRAINT cart_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
ADD CONSTRAINT cart_ibfk_2 FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE;

-- Verify success
SELECT 'Cart table updated successfully' AS result; 