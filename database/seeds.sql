--
-- Database: `vendorbridge`
--
--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'admin', 'System administrator', '2026-06-06 04:11:23'),
(2, 'procurement_officer', 'Creates RFQs and manages procurement', '2026-06-06 04:11:23'),
(3, 'manager', 'Approves or rejects quotations', '2026-06-06 04:11:23'),
(4, 'vendor', 'Submits quotations for RFQs', '2026-06-06 04:11:23');
# Create the database folder
mkdir database

