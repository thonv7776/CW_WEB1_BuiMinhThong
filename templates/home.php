<?php
/**
 * Home landing page. Static content only — no DB access here.
 */
?>
<section class="hero">
    <h1>Ask questions. Help each other. Learn faster.</h1>
    <p class="lead">
        A place for students to post the programming and coursework questions
        they’re stuck on — tagged to the right module and author — and to read
        what everyone else has already asked.
    </p>
    <div class="btn-row">
        <a class="btn btn-primary" href="questions.php">Browse questions</a>
        <a class="btn btn-secondary" href="questions-add.php">Ask a question</a>
    </div>
</section>

<ul class="index">
    <li class="index-item">
        <h3>Browse questions</h3>
        <p>Every question with its author and module, newest first — open the ones that match what you’re working on.</p>
        <a class="go" href="questions.php">View the list &rarr;</a>
    </li>
    <li class="index-item">
        <h3>Ask a question</h3>
        <p>Post a new question with an optional screenshot, and tag it to your module so the right people find it.</p>
        <a class="go" href="questions-add.php">Start writing &rarr;</a>
    </li>
    <li class="index-item">
        <h3>Contact the team</h3>
        <p>Spotted a problem or need a hand with your account? Send a message straight to the site admins.</p>
        <a class="go" href="contact.php">Get in touch &rarr;</a>
    </li>
</ul>
