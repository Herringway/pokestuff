<?php
class Penguin_Twig_Extensions extends Twig_Extension
{
	private $langcache;
    public function getFilters()
    {
        return array(
            'gravatar' => new Twig_Filter_Method($this, 'gravatar'),
			'localize' => new Twig_Filter_Method($this, 'localize')
        );
    }
	public function getName() {
		return 'Penguin\'s Twig Extensions';
	}

    public function gravatar($email, $default = 'retro', $size = 100, $rating = 'g') {
		return sprintf("http://www.gravatar.com/avatar/%s?d=%s&s=%s&r=%s", md5(strtolower(trim($email))),urlencode($default),$size, $rating);
	}
	public function localize($stringid, $language) {
		return '';
	}
}
?>