<?php /* Page: categories — included by index.php when ?page=categories */ ?>
    <!-- Categories Section -->
    <div class="data-table">
        <div class="table-title">Product Categories</div>
        <?php if(count($categories) > 0): ?>
        <table>
            <thead><tr><th>ID</th><th>Category Name</th></tr></thead>
            <tbody>
                <?php foreach($categories as $c): ?>
                <tr><td><?= $c['category_id'] ?></td><td><?= htmlspecialchars($c['category_name']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">No categories</div>
        <?php endif; ?>
    </div>
    
    <div class="form-card">
        <h3>Add New Category</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_category">
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="category_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
    </div>
    
