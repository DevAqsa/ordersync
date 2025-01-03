# OrderSync - Simplified Order Management Plugin for WordPress

**OrderSync** is a user-friendly WordPress plugin that helps service providers, freelancers, and small businesses manage orders, track progress, and streamline communication with clients. With secure tracking, intuitive design, and real-time updates, OrderSync is the perfect solution for managing projects effortlessly.

---

## **Features**

### 1. **Client-Facing Order Form**
- Collects essential details like name, contact information, service type, delivery preferences, and budget.
- Supports file attachments for project references.
- Auto-generates a unique order tracking URL with password protection.

### 2. **Order Tracking Page**
- Minimalistic design focused on clarity and usability.
- Displays:
  - Order details and status.
  - Admin updates in a timeline structure.
  - Threaded comment system for seamless communication.
- Password-protected for security.

### 3. **Admin Dashboard**
- Overview of all orders with sorting and filtering options.
- Detailed order view with actions like:
  - Status updates.
  - Commenting (with "internal only" option).
  - Editing order details.
- Notifications for new comments and updates.

### 4. **Real-Time Comments**
- AJAX-powered threaded comments for real-time updates.
- Secure and sanitized inputs for safe interactions.

---

## **Technical Highlights**
- **Custom Post Type**: Uses "Orders" as a custom post type for storing order data.
- **Meta Fields**: Stores custom data like passwords, statuses, and comments.
- **Shortcodes**: Generate forms and tracking pages with ease.
- **Security**: Includes nonce validation, input sanitization, and secure file uploads.
- **Email Notifications**: Sends order confirmations and updates via `wp_mail`.
- **Mobile-Responsive Design**: Ensures usability on all devices.

---

## **Installation**

1. **Download and Install**:
   - Upload the plugin folder to the `/wp-content/plugins/` directory.
   - Activate the plugin through the 'Plugins' menu in WordPress.

2. **Configure Settings**:
   - Navigate to the **OrderSync** settings page in the WordPress admin dashboard.
   - Set default statuses, notification preferences, and terms & conditions.

3. **Add to Your Site**:
   - Use `[ordersync_form]` shortcode to display the order form on any page or post.
   - Use `[ordersync_tracking]` shortcode for the order tracking page.

---

## **Usage**

### For Clients:
1. Fill out the order form with required details.
2. Receive a unique order tracking URL and password.
3. Track progress, communicate via comments, and receive updates.

### For Admins:
1. Manage all orders from the admin dashboard.
2. Update statuses, add comments, and notify clients in real-time.
3. Use filters to prioritize and sort orders.

---

## **Learning Outcomes (For Developers)**

- **Frontend**: Form creation, AJAX updates, and responsive design.
- **Backend**: Custom post types, meta fields, and REST API integration.
- **Security**: Password protection, input validation, and secure file handling.
- **UI/UX**: Intuitive interfaces for client and admin users.

---

## **Support**

For support and inquiries, contact us at [support@ordersyncplugin.com](mailto:support@ordersyncplugin.com).

---

## **Contributions**

We welcome contributions! If you'd like to contribute to OrderSync, please fork the repository and submit a pull request.

---

## **License**

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html). 

---

Thank you for using **OrderSync** – simplifying order management for everyone!
