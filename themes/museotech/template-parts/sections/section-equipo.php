<?php $bio = museotech_field('bio'); ?>
<?php if ($bio) : ?><div class="team-bio"><?php echo wp_kses_post($bio); ?></div><?php endif; ?>