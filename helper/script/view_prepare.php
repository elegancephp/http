<?php

use Elegance\View;

View::prepare('view', fn ($viweRef) => view($viweRef));

View::prepare('this.view', fn ($viweRef) => viewIn($viweRef));