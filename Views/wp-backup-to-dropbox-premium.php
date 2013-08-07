<?php
/**
 * This file contains the contents of the Dropbox admin monitor page.
 *
 * @copyright Copyright (C) 2011-2013 Michael De Wild. All rights reserved.
 * @author Michael De Wildt (http://www.mikeyd.com.au/)
 * @license This program is free software; you can redistribute it and/or modify
 *          it under the terms of the GNU General Public License as published by
 *          the Free Software Foundation; either version 2 of the License, or
 *          (at your option) any later version.
 *
 *          This program is distributed in the hope that it will be useful,
 *          but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          GNU General Public License for more details.
 *
 *          You should have received a copy of the GNU General Public License
 *          along with this program; if not, write to the Free Software
 *          Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA.
 */
$manager = WP_Backup_Registry::extension_manager();

$wpb2d = $manager->get_url();
$installUrl = $manager->get_install_url();
$buyUrl = $manager->get_buy_url();

$extensions = $error = $title = null;
if (isset($_REQUEST['error']))
	$error = sprintf(__('There was an error with your payment, please contact %s to resolve.'), '<a href="mailto:michael.dewildt@gmail.com">Mikey</a>');

if (isset($_REQUEST['title']))
	$success = sprintf(__('You have succesfully purchased the %s premium extension, please install it below.'), "<strong>{$_REQUEST['title']}</strong>");

try {
	if (isset($_POST['name'])) {
		$ext = $manager->install($_POST['name']);
		$slug = $manager->get_menu_slug($ext);
		$title = $ext->get_menu();

		?><script type='text/javascript'>
			jQuery(document).ready(function ($) {
				jQuery('a[href$="backup-to-dropbox-premium"]').parent().before('<li><a href="admin.php?page=<?php echo $slug ?>"><?php echo $title ?></a></li>');
			});
		</script>'<?php
	}

	$extensions = $manager->get_extensions();
} catch (Exception $e) {
	$error = $e->getMessage();
}

?>
<div class="wrap premium">
	<div class="icon32"><img width="36px" height="36px"
								 src="<?php echo $uri ?>/Images/WordPressBackupToDropbox_64.png"
								 alt="WordPress Backup to Dropbox Logo"></div>
	<h2><?php _e('WordPress Backup to Dropbox', 'wpbtd'); ?></h2>
	<p class="description"><?php printf(__('Version %s', 'wpbtd'), BACKUP_TO_DROPBOX_VERSION) ?></p>
	<h3><?php _e('Premium Extensions', 'wpbtd'); ?></h3>
	<div>
		<p>
			<?php _e('Welcome to Premium Extensions. Please choose an extension below to enhance WordPress Backup to Dropbox.', 'wpbtd'); ?>
			<?php _e('Installing a premium extensions is easy:', 'wpbtd'); ?>
		</p>
		<ol class="instructions">
			<li><?php _e('Click Buy Now and pay using PayPal', 'wpbtd'); ?></li>
			<li><?php _e('Click Install Now to download and install the extension', 'wpbtd'); ?></li>
			<li><?php _e('Thats it, options for your extension will be available in the menu on the left', 'wpbtd'); ?></li>
		</ol>
		<a class="paypal" href="#" onclick="javascript:window.open('https://www.paypal.com/au/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350');">
			<img  src="https://www.paypalobjects.com/en_AU/i/bnr/horizontal_solution_PP.gif" border="0" alt="Solution Graphics">
		</a>

		<a class="moneyback" href="http://wpb2d.com/money-back-guarantee">
			<img src="<?php echo $uri ?>/Images/guarantee.gif" alt="<?php _e('100% money back guarantee') ?>"/>
		</a>

	</div>
	<div class="errors">
		<?php if ($error): ?>
			<p class="error">
				<?php echo esc_attr($error) ?>
			</p>
		<?php elseif (isset($success)): ?>
			<p class="success">
				<?php echo $success ?>
			</p>
		<?php endif; ?>
	</div>
	<table id="extensions">
		<tr>
			<th><?php _e('Name') ?></th>
			<th><?php _e('Description') ?></th>
			<th><?php _e('Price') ?></th>

			<?php if (!$manager->is_new_site()): ?>
				<th><?php _e('Exipry') ?></th>
			<?php endif; ?>

			<th></th>
		</tr>

		<?php if (is_array($extensions)) foreach ($extensions as $extension): ?>
		<tr>
			<td><?php echo esc_attr($extension['name']) ?></td>
			<td><?php echo esc_attr($extension['description']) ?></td>
			<td>$<?php echo esc_attr($extension['price']) ?> USD</td>

			<?php if (!$manager->is_new_site()): ?>
				<?php if ($extension['expiry'] == 'expired'): ?>
					<td><?php _e('Updates for this extension have expired, please make a new purchase to get another year of updates.') ?></td>
				<?php else: ?>
					<td><?php echo date_i18n(get_option('date_format'), $extension['expiry']) ?></td>
				<?php endif; ?>
			<?php endif; ?>

			<td>
				<form action="<?php echo $extension['expiry'] == 'expired' ? $buyUrl : $installUrl; ?>" method="post" id="extension-<?php echo esc_attr($extension['name']) ?>">
					<input type="hidden" value="<?php echo esc_attr($extension['name']); ?>" name="name" />
					<input type="hidden" value="<?php echo get_site_url() ?>" name="site" />
					<?php if ($extension['expiry'] != 'expired' && $manager->is_installed($extension['name'])): ?>
						<span class="installed">Installed and up-to-date</span>
					<?php else: ?>
						<input class="button-primary" type="submit" value="<?php echo $extension['expiry'] == 'expired' ? __('Buy Now') : __('Install Now'); ?>" class="submitBtn" />
					<?php endif; ?>
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<strong><?php _e('Please Note:') ?></strong>&nbsp;
		<?php echo sprintf(__('Each payment includes updates and support on a single website for one year. If you manage multiple websites please consider purchasing an %s.'), '<a href="http://wpb2d.com/buy-subscription">' . __('unlimited site subscription') . '</a>') ?>
	</p>
</div>
