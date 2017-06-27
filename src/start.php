<?php

jaxon()->sentry()->addViewRenderer('dwoo', function () {
    return new Jaxon\Dwoo\View();
});
