<div class="ordersync-form-wrapper">
    <form id="ordersync-form" class="ordersync-form" enctype="multipart/form-data">
        <?php wp_nonce_field('ordersync-submit-order', 'ordersync_nonce'); ?>

        <div class="form-section">
            <h3>Client Information</h3>
            
            <div class="form-group">
                <label for="client_name">Full Name *</label>
                <input type="text" id="client_name" name="client_name" required>
            </div>

            <div class="form-group">
                <label for="client_email">Email Address *</label>
                <input type="email" id="client_email" name="client_email" required>
            </div>

            <div class="form-group">
                <label for="client_phone">Phone Number</label>
                <input type="tel" id="client_phone" name="client_phone">
            </div>
        </div>

        <div class="form-section">
            <h3>Project Details</h3>

            <div class="form-group">
                <label for="project_type">Project Type *</label>
                <select id="project_type" name="project_type" required>
                    <option value="">Select Project Type</option>
                    <option value="web_development">Web Development</option>
                    <option value="graphic_design">Graphic Design</option>
                    <option value="digital_marketing">Digital Marketing</option>
                    <option value="content_writing">Content Writing</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Project Description *</label>
                <textarea id="description" name="description" rows="5" required></textarea>
                <small>Please provide detailed information about your project requirements</small>
            </div>

            <div class="form-group">
                <label for="project_files">Project Files</label>
                <input type="file" id="project_files" name="project_files[]" multiple>
                <small>Upload any relevant files (Max size: 10MB)</small>
            </div>
        </div>

        <div class="form-section">
            <h3>Timeline & Budget</h3>

            <div class="form-group">
                <label for="delivery_date">Preferred Delivery Date *</label>
                <input type="date" id="delivery_date" name="delivery_date" required>
            </div>

            <div class="form-group">
                <label for="priority">Priority Level</label>
                <select id="priority" name="priority">
                    <option value="normal">Normal</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <div class="form-group">
                <label for="budget">Budget Range (USD)</label>
                <input type="number" id="budget" name="budget" min="0" step="100">
            </div>
        </div>

        <div class="form-section">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required>
                    I agree to the terms and conditions *
                </label>
            </div>

            <div class="form-submit">
                <button type="submit" class="submit-button">Submit Order</button>
            </div>
        </div>
    </form>
</div>