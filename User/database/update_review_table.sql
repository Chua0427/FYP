-- SQL migration to add order_id column to review table for per-order reviews
ALTER TABLE review
  ADD COLUMN order_id INT NOT NULL AFTER product_id,
  ADD CONSTRAINT fk_review_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE; 