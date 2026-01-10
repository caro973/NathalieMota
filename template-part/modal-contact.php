<div id="contact-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="jQuery('#contact-modal').fadeOut();">&times;</span>
        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/Contact.png'); ?>"
     alt="Image de contact">
     
        <?php echo do_shortcode('[contact-form-7 id="e1af9a0" title="Formulaire de contact 1"]'); ?>
    </div>
</div>
