<?php
declare(strict_types=1);

use App\Html;

/**
 * @var string $orgName
 * @var string $username
 * @var string $today
 * @var string $nonce
 */
?>
<form id="release-form">
    <input type="hidden" name="u" value="<?= Html::e($username) ?>">

    <label for="name">Name</label>
    <input type="text" id="name" name="name" maxlength="200" required
           autocomplete="name" autocapitalize="words" pattern=".*\S.*"
           title="Please enter your name.">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" maxlength="200" required
           autocomplete="email" inputmode="email" autocapitalize="off" spellcheck="false"
           title="Please enter a valid email address.">

    <label for="date">Date</label>
    <input type="date" id="date" name="date" value="<?= Html::e($today) ?>" required>

    <label for="photo_number">Photo Number</label>
    <input type="text" id="photo_number" name="photo_number" maxlength="200" required
           inputmode="text" pattern=".*\S.*" title="Please enter the photo number.">

    <label class="check" for="disclaimer">
        <input type="checkbox" id="disclaimer" name="disclaimer" required>
        <span>I hereby give the <?= Html::e($orgName) ?> permission to use any photos/video in
        which I or my child/ward appear without incurring debt or liabilities of any kind.</span>
    </label>

    <div id="error" class="msg error" hidden></div>
    <div id="thanks" class="msg success" hidden>Form submitted. You should receive an email record &mdash; check your Spam folder if you can't find it. Thank you!</div>

    <button type="submit" id="submit-btn">
        <span class="btn-label">Submit</span>
        <span class="btn-spinner spinner" aria-hidden="true"></span>
    </button>
</form>

<script nonce="<?= Html::e($nonce) ?>">
(function () {
    var form = document.getElementById('release-form');
    var button = document.getElementById('submit-btn');
    var thanks = document.getElementById('thanks');
    var error = document.getElementById('error');
    var tokenEl = document.querySelector('meta[name="csrf-token"]');
    var token = tokenEl ? tokenEl.getAttribute('content') : '';

    function setLoading(on) {
        button.disabled = on;
        button.classList.toggle('is-loading', on);
    }

    function fail(msg) {
        setLoading(false);
        error.textContent = msg;
        error.hidden = false;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        error.hidden = true;
        if (!form.checkValidity()) { form.reportValidity(); return; }

        setLoading(true);

        fetch(window.location.pathname + window.location.search, {
            method: 'POST',
            headers: { 'X-CSRF-Token': token },
            body: new FormData(form)
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (res) {
            if (!res.ok || !res.d.ok) { fail((res.d && res.d.error) || 'Submission failed. Please try again.'); return; }
            setLoading(false);
            button.hidden = true;
            thanks.hidden = false;
        })
        .catch(function () { fail('A network error occurred. Please try again.'); });
    });
})();
</script>
