<?php
get_header();

$members = [];
while (have_posts()) {
    the_post();
    $roles = wp_get_post_terms(get_the_ID(), 'rol', ['fields' => 'names']);
    $bio = (string) museotech_field('bio', get_the_ID());
    $resources = [];
    if (preg_match_all('~<a\s[^>]*href=(["\'])(.*?)\1[^>]*>(.*?)</a>~is', $bio, $resource_matches, PREG_SET_ORDER)) {
        foreach ($resource_matches as $resource_match) {
            $resources[] = [
                'url' => esc_url_raw(html_entity_decode($resource_match[2])),
                'label' => wp_strip_all_tags($resource_match[3]),
            ];
        }
    }
    $bio_without_resources = preg_replace('~<a\s[^>]*href=(["\'])(.*?)\1[^>]*>(.*?)</a>~is', '', $bio);
    $members[] = [
        'id' => get_the_ID(),
        'name' => get_the_title(),
        'role' => ! is_wp_error($roles) && $roles ? implode(' · ', $roles) : __('Equipo Museotech', 'museotech'),
        'thumbnail_id' => get_post_thumbnail_id(),
        'bio' => trim((string) $bio_without_resources),
        'resources' => $resources,
        'permalink' => get_permalink(),
    ];
}
?>
<main id="main-content" class="team-page" data-team-count="<?php echo esc_attr((string) count($members)); ?>">
    <?php if ($members) : ?>
        <div class="team-page__overlay" aria-hidden="true">
            <?php foreach ($members as $index => $member) : ?>
                <?php $slide_number = $index + 1; ?>
                <div class="team-page__slide team-page__marquee team-page__marquee--tone-<?php echo esc_attr((string) (($index % 4) + 1)); ?><?php echo $index === 0 ? ' is-active' : ''; ?>" data-team-slide="<?php echo esc_attr((string) $slide_number); ?>">
                    <p><?php echo esc_html(implode(' ', array_fill(0, 3, $member['name']))); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="team-page__modal">
            <div class="team-page__images">
                <?php foreach ($members as $index => $member) : ?>
                    <div class="team-page__image<?php echo $index === 0 ? ' is-active' : ''; ?>" data-team-image="<?php echo esc_attr((string) ($index + 1)); ?>">
                        <?php if ($member['thumbnail_id']) : ?>
                            <?php
                            echo wp_get_attachment_image(
                                $member['thumbnail_id'],
                                'full',
                                false,
                                [
                                    'alt' => $member['name'],
                                    'loading' => $index === 0 ? 'eager' : 'lazy',
                                    'fetchpriority' => $index === 0 ? 'high' : 'auto',
                                ]
                            );
                            ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button
                class="team-page__profile-trigger"
                type="button"
                data-profile-label="<?php esc_attr_e('Ver ficha de', 'museotech'); ?>"
                aria-label="<?php echo esc_attr(sprintf(__('Ver ficha de %s', 'museotech'), $members[0]['name'])); ?>"
            >
                <span class="team-page__profile-hint" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                    <span><?php esc_html_e('Ver ficha', 'museotech'); ?></span>
                </span>
            </button>
            <div class="team-page__info" id="team-page-info" role="tabpanel" aria-labelledby="team-member-tab-1" tabindex="0" aria-live="polite">
                <h1 class="team-page__name"><?php echo esc_html($members[0]['name']); ?></h1>
                <p class="team-page__role"><?php echo esc_html($members[0]['role']); ?></p>
            </div>
            <div class="team-page__thumbnails" role="tablist" aria-label="<?php esc_attr_e('Seleccionar miembro del equipo', 'museotech'); ?>">
                <div class="team-page__thumbnail-track" role="presentation">
                    <?php foreach ($members as $index => $member) : ?>
                        <button
                            class="team-page__thumbnail<?php echo $index === 0 ? ' is-active' : ''; ?>"
                            type="button"
                            role="tab"
                            id="team-member-tab-<?php echo esc_attr((string) ($index + 1)); ?>"
                            aria-controls="team-page-info"
                            tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>"
                            data-team-target="<?php echo esc_attr((string) ($index + 1)); ?>"
                            aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                            aria-label="<?php echo esc_attr($member['name']); ?>"
                        >
                            <?php if ($member['thumbnail_id']) : ?>
                                <?php echo wp_get_attachment_image($member['thumbnail_id'], 'thumbnail', false, ['alt' => '', 'loading' => 'lazy']); ?>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <button class="team-page__control team-page__control--previous" type="button" aria-label="<?php esc_attr_e('Ver miembro anterior', 'museotech'); ?>"></button>
        <button class="team-page__control team-page__control--next" type="button" aria-label="<?php esc_attr_e('Ver miembro siguiente', 'museotech'); ?>"></button>

        <div class="team-page__cursor" aria-hidden="true">
            <svg viewBox="0 0 32 32"><path d="M27 16H5M12 9l-7 7 7 7"></path></svg>
        </div>

        <dialog class="team-profile-dialog" aria-labelledby="team-profile-dialog-title">
            <div class="team-profile-dialog__shell">
                <h2 id="team-profile-dialog-title" class="team-profile-dialog__title"><?php esc_html_e('Ficha del equipo', 'museotech'); ?></h2>
                <button class="team-profile-dialog__close" type="button" aria-label="<?php esc_attr_e('Cerrar ficha', 'museotech'); ?>">×</button>
                <?php foreach ($members as $index => $member) : ?>
                    <article class="team-profile-dialog__panel" data-profile-panel="<?php echo esc_attr((string) ($index + 1)); ?>"<?php echo $index === 0 ? '' : ' hidden'; ?>>
                        <div class="team-profile-dialog__image">
                            <?php if ($member['thumbnail_id']) : ?>
                                <?php echo wp_get_attachment_image($member['thumbnail_id'], 'large', false, ['alt' => $member['name'], 'loading' => 'lazy']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="team-profile-dialog__content">
                            <div class="team-profile-dialog__meta">
                                <p class="team-profile-dialog__eyebrow"><?php echo esc_html($member['role']); ?></p>
                                <p class="team-profile-dialog__counter"><?php echo esc_html(sprintf('%02d / %02d', $index + 1, count($members))); ?></p>
                            </div>
                            <h3><?php echo esc_html($member['name']); ?></h3>
                            <?php if ($member['bio']) : ?>
                                <div class="team-profile-dialog__bio">
                                    <?php echo wpautop(wp_kses_post($member['bio'])); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($member['resources']) : ?>
                                <div class="team-profile-dialog__resources" aria-label="<?php esc_attr_e('Enlaces profesionales', 'museotech'); ?>">
                                    <?php foreach ($member['resources'] as $resource) : ?>
                                        <a class="ui-button ui-button--resource" href="<?php echo esc_url($resource['url']); ?>" target="_blank" rel="noopener noreferrer">
                                            <span><?php echo esc_html($resource['label']); ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </dialog>

        <script type="application/json" class="team-page__data">
            <?php echo wp_json_encode(array_map(static fn(array $member): array => ['name' => $member['name'], 'role' => $member['role']], $members)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </script>
    <?php else : ?>
        <p class="team-page__empty"><?php esc_html_e('Todavía no hay miembros publicados.', 'museotech'); ?></p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
