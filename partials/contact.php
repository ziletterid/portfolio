<section class="contact" id="contact" data-theme="dark">
    <div class="contact__arc" aria-hidden="true"><span></span></div>
    <div class="contact__grid">
        <div class="contact__lead">
            <span class="eyebrow">(07) Contact</span>
            <h2 class="display reveal-words"><?= words('Got a brand that deserves to stand out?') ?></h2>
            <div class="contact__mail">
                <a href="mailto:<?= e($p['email']) ?>" data-cursor="Write"><?= e($p['email']) ?></a>
                <button type="button" class="copy" data-copy="<?= e($p['email']) ?>"><span>Copy</span></button>
            </div>
            <?php if (!empty($p['whatsapp'])): ?>
                <a class="btn btn--wa" href="https://wa.me/<?= e(preg_replace('/\D+/', '', $p['whatsapp'])) ?>" target="_blank" rel="noopener" data-magnetic>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.4.1-.2 0-.3 0-.5l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.8 11.9 11.9 0 0 0 4.6 4c1.7.7 2.4.8 3.2.7.5-.1 1.5-.6 1.8-1.2.2-.6.2-1.1.1-1.2l-.4-.2Z"/></svg>
                    <span>Chat on WhatsApp</span>
                </a>
            <?php endif; ?>
            <p class="contact__note">Based in Karanganyar, working with clients all over Indonesia. Replies within one working day.</p>
        </div>

        <form class="form" action="api/contact.php" method="post" novalidate data-form>
            <fieldset class="form__chips">
                <legend>I need help with</legend>
                <?php foreach (['Logo & branding', 'Packaging', 'Print & promotion', 'Social media', 'UI/UX'] as $k => $opt): ?>
                    <label><input type="checkbox" name="topics[]" value="<?= e($opt) ?>" <?= $k === 0 ? 'checked' : '' ?>><span><?= e($opt) ?></span></label>
                <?php endforeach; ?>
            </fieldset>
            <div class="field">
                <input id="f-name" name="name" type="text" autocomplete="name" placeholder=" " required maxlength="120">
                <label for="f-name">Your name</label>
                <small class="field__err" aria-live="polite"></small>
            </div>
            <div class="field">
                <input id="f-email" name="email" type="email" autocomplete="email" placeholder=" " required maxlength="160">
                <label for="f-email">Email</label>
                <small class="field__err" aria-live="polite"></small>
            </div>
            <fieldset class="form__chips form__chips--budget">
                <legend>Budget (IDR)</legend>
                <?php foreach (['< 2 jt', '2–5 jt', '5–15 jt', '15 jt+'] as $k => $opt): ?>
                    <label><input type="radio" name="budget" value="<?= e($opt) ?>" <?= $k === 1 ? 'checked' : '' ?>><span><?= e($opt) ?></span></label>
                <?php endforeach; ?>
            </fieldset>
            <div class="field">
                <textarea id="f-msg" name="message" rows="4" placeholder=" " required maxlength="4000"></textarea>
                <label for="f-msg">Tell me about the project</label>
                <small class="field__err" aria-live="polite"></small>
            </div>
            <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="form__foot">
                <button class="btn btn--solid btn--lg" type="submit" data-magnetic>
                    <span class="btn__label">Send message</span>
                    <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <p class="form__status" role="status" aria-live="polite"></p>
            </div>
        </form>
    </div>
</section>
