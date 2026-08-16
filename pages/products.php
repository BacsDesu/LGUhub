<?php /* Page: products — included by index.php when ?page=products */ ?>
    <!-- Products Section -->
    <div class="data-table">
        <div class="table-title">Product List</div>
        <?php if(count($products) > 0): ?>
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th></tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td><?= $p['product_id'] ?></td>
                    <td><?= htmlspecialchars($p['product_name']) ?></td>
                    <td><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
                    <td>₱ <?= number_format($p['price'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">No products found</div>
        <?php endif; ?>
    </div>
    
    <div class="form-card">
        <h3>Add New Product</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_product">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" required>
            </div>
            <div class="form-group">
                <label>Price (₱)</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach($categories as $c): ?>
                    <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
    
