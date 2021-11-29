<?php

namespace Jugid\Finflector;

use InvalidArgumentException;

class Pluralizer {

    const EMPTY_ARTICLE = '';

    /** If detection is not part of this array, just add a 's' */
    private const GLOBAL_RULES = [
        'eu' => 'eux',
        'eau' => 'eaux',
        'au' => 'aux',
        'ou' => 'ous',
        'ail'=> 'aux',
        'al' => 'aux'
    ];

    /** The order is important */
    private const GLOBAL_ARTICLES = [
        'de la' => 'des', 
        'de l\'' => 'des',
        'du' => 'des', 
        'de' => 'des', 
        'un' => 'des', 
        'une' => 'des',
        'le'=> 'les',
        'la'=> 'les',
        'l\''=> 'les',
    ];

    /** Words that end with this letters are not converted */
    private const NOT_PLURALIZE = ['s', 'x', 'z'];

    /**
     * Words that are not under global rules
     */
    private const SPECIAL = [
        'oeil' => 'yeux',
        'bijou' => 'bijoux',
        'chou' => 'choux',
        'caillou' => 'cailloux',
        'genou'=> 'genoux',
        'hibou' => 'hiboux',
        'joujou' => 'joujoux',
        'ripou'=> 'ripoux',
        'chouchou'=>'chouchoux',
        'boutchou'=> 'boutchoux',
        'pou' => 'poux',
        'chou' => 'choux',
        'mille'=>'mille',
        'ail' => 'ails',
        'aval' => 'avals', 
        'bal' => 'bals', 
        'carnaval' => 'carnavals', 
        'chacal' => 'chacals', 
        'corral' => 'corrals', 
        'festival' => 'festivals', 
        'récital', 'récitals', 
        'régal' => 'régals',
        'bleu' => 'bleus',
        'émeu' => 'émeus', 
        'landau' => 'landaus', 
        'pneu'=>'pneus', 
        'sarrau'=>'sarraus',
        'aval'=>'avals',
        'bal'=>'bals',
        'banal'=>'banals',
        'bancal'=>'bancals',
        'cal'=>'cals',
        'carnaval'=>'carnavals',
        'cérémonial'=>'cérémonials',
        'choral'=>'chorals',
        'étal'=>'étals',
        'fatal'=>'fatals',
        'natal'=>'natals',
        'naval'=>'navals',
        'pal'=>'pals',
        'récital'=>'récitals',
        'régal'=>'régal',
        'tonal'=>'tonals',
        'val'=>'vals',
        'virginal'=>'virginals'
    ];

    private string $plural;

    public function pluralize(string $words) : string 
    {
        $is_ucfirst = ctype_upper($words[0]);
        $words = strtolower($words);
        $singular = $this->getSingular($words);
        $article_plural = $this->getArticlePlural($words);
        
        if($this->isSpecialWord($singular)) {
            $special_plural = self::SPECIAL[$singular];
            return $this->getFinalWords($article_plural, $special_plural, $is_ucfirst);
        }

        $singular_explode = explode(' ', $singular);
        $singular = $singular_explode[0];
        $this->plural = $this->getPluralFromGlobalRules($singular);

        if($this->plural === $singular && $this->shouldNotBePluralized($singular)) {
            $this->plural = $singular . 's';
        }        

        $singular_explode[0] = $this->plural;
        $final_plural = implode(' ', $singular_explode);

        return $this->getFinalWords($article_plural, $final_plural, $is_ucfirst);
    }

    private function getFinalWords(string $article, string $plural, bool $uppercase) : string {
        
        if(empty($article)) {
            return $uppercase ? ucfirst($plural) : $plural;
        } else {
            return $uppercase ? ucfirst($article . ' ' . $plural) : $article . ' ' . $plural;
        }       
    }

    private function getPluralFromGlobalRules(string $singular) : string {
        foreach(self::GLOBAL_RULES as $singular_end => $plural_end) {

            $plural = $this->getPluralFor($singular, $singular_end, $plural_end);
            
            if(empty($plural)) { continue; }
            
            return $plural;
        }

        return $singular;
    }

    private function getPluralFor($singular, $singular_end, $plural_end) {
        if(!$this->hasValidEndFor($singular, $singular_end)) {
            return '';
        }
        
        $singular_without_end = substr($singular, 0, strlen($singular) - strlen($singular_end));
        return $singular_without_end . $plural_end;
    }

    private function getSingular(string $words) : string {
        foreach(array_keys(self::GLOBAL_ARTICLES) as $article_rule) {
            $article = substr($words, 0, strlen($article_rule));

            if($article !== $article_rule) {
                continue;
            }

            $last_article_character = substr($words, strlen($article_rule)-1, 1);
            $article_offset = ($last_article_character == '\'') ? strlen($article) : strlen($article) + 1;
            return substr($words, $article_offset);
        }

        return $words;
    }

    private function getArticlePlural(string $words) : string {
        foreach(self::GLOBAL_ARTICLES as $article_rule => $plural_rule) {
            if(substr($words, 0, strlen($article_rule)) === $article_rule) {
                return $plural_rule;
            }
        }

        return self::EMPTY_ARTICLE;
    }

    private function shouldNotBePluralized(string $word) {
        return !in_array(substr($word, -1), self::NOT_PLURALIZE);
    }

    private function hasValidEndFor(string $singular, string $singular_end) : bool {
        return substr($singular, strlen($singular_end) * -1 ) == $singular_end;
    }

    private function isSpecialWord(string $word) : bool {
        return array_key_exists($word, self::SPECIAL);
    }
}