<?php

/**
 * korea-visa
 * कोरिया भिसा केन्द्र – Korea Visa Information Center
 *
 * @package Sngine
 */

require('bootloader.php');

if ($user->_logged_in || !$system['system_public']) {
  user_access();
}

try {

  $visas = [
    // ── Study ──────────────────────────────────────────────────────────────
    [
      'code' => 'D-2',
      'name' => 'विद्यार्थी भिसा',
      'name_en' => 'Student Visa',
      'desc' => 'कोरियाली विश्वविद्यालय वा कलेजमा स्नातक, स्नातकोत्तर वा पिएचडी अध्ययनका लागि।',
      'cat'  => 'अध्ययन',
      'cat_en' => 'Study',
      'icon' => 'graduation-cap',
      'color' => '#5e72e4',
      'stay' => 'आवश्यकता अनुसार (नवीकरणयोग्य)',
      'link' => 'https://www.hikorea.go.kr',
    ],
    [
      'code' => 'D-4',
      'name' => 'भाषा प्रशिक्षण भिसा',
      'name_en' => 'Language Training Visa',
      'desc' => 'कोरियाली भाषा सिक्न मान्यता प्राप्त भाषा केन्द्रमा भर्ना भएकाहरूका लागि।',
      'cat'  => 'अध्ययन',
      'cat_en' => 'Study',
      'icon' => 'language',
      'color' => '#5e72e4',
      'stay' => '१–२ वर्ष (नवीकरणयोग्य)',
      'link' => 'https://www.hikorea.go.kr',
    ],
    // ── Employment ─────────────────────────────────────────────────────────
    [
      'code' => 'E-9',
      'name' => 'गैर-पेशेवर रोजगार (EPS)',
      'name_en' => 'Non-professional Employment',
      'desc' => 'EPS-TOPIK परीक्षामा उत्तीर्ण भई उत्पादन, निर्माण, कृषि वा मत्स्य क्षेत्रमा काम गर्न।',
      'cat'  => 'रोजगार',
      'cat_en' => 'Employment',
      'icon' => 'industry',
      'color' => '#2dce89',
      'stay' => '३ वर्ष (१ वर्ष ९ महिना थप्न सकिन्छ)',
      'link' => 'https://www.eps.go.kr',
    ],
    [
      'code' => 'E-7',
      'name' => 'विशेष व्यावसायिक',
      'name_en' => 'Specially Designated Activities',
      'desc' => 'IT, इन्जिनियरिङ, व्यवस्थापन जस्ता विशेष सीप र योग्यता भएकाहरूका लागि।',
      'cat'  => 'रोजगार',
      'cat_en' => 'Employment',
      'icon' => 'briefcase',
      'color' => '#2dce89',
      'stay' => '१–३ वर्ष (नवीकरणयोग्य)',
      'link' => 'https://www.hikorea.go.kr',
    ],
    [
      'code' => 'H-2',
      'name' => 'कार्य भ्रमण',
      'name_en' => 'Working Holiday',
      'desc' => 'विदेशी कोरियन वंशजका लागि। उत्पादन, निर्माण, सेवा क्षेत्रमा काम गर्न सकिन्छ।',
      'cat'  => 'रोजगार',
      'cat_en' => 'Employment',
      'icon' => 'suitcase',
      'color' => '#2dce89',
      'stay' => '१ वर्ष ११ महिना (अधिकतम)',
      'link' => 'https://www.hikorea.go.kr',
    ],
    // ── Visit ──────────────────────────────────────────────────────────────
    [
      'code' => 'C-3',
      'name' => 'अल्पकालीन भ्रमण',
      'name_en' => 'Short-term Visit',
      'desc' => 'पर्यटन, परिवार भेट, व्यापारिक वार्ता वा सांस्कृतिक कार्यक्रमका लागि।',
      'cat'  => 'भ्रमण',
      'cat_en' => 'Visit',
      'icon' => 'plane',
      'color' => '#fb6340',
      'stay' => '९० दिन सम्म',
      'link' => 'https://www.hikorea.go.kr',
    ],
    [
      'code' => 'C-3-9',
      'name' => 'चिकित्सा भ्रमण',
      'name_en' => 'Medical Tourism',
      'desc' => 'कोरियामा चिकित्सा उपचार र स्वास्थ्य सेवाका लागि।',
      'cat'  => 'भ्रमण',
      'cat_en' => 'Visit',
      'icon' => 'medkit',
      'color' => '#fb6340',
      'stay' => '९० दिन (थप्न सकिन्छ)',
      'link' => 'https://www.hikorea.go.kr',
    ],
    // ── Family / Residence ─────────────────────────────────────────────────
    [
      'code' => 'F-1',
      'name' => 'परिवार सहवास',
      'name_en' => 'Family Stay',
      'desc' => 'कोरियामा बस्दै आएका परिवारका सदस्यसँग बस्नका लागि।',
      'cat'  => 'परिवार/बसोबास',
      'cat_en' => 'Family',
      'icon' => 'home',
      'color' => '#f5365c',
      'stay' => 'स्पोन्सरको भिसा अनुसार',
      'link' => 'https://www.hikorea.go.kr',
    ],
    [
      'code' => 'F-6',
      'name' => 'विवाह आप्रवासी',
      'name_en' => 'Marriage Migrant',
      'desc' => 'कोरियाली नागरिकसँग विवाह गरेका विदेशी नागरिकका लागि।',
      'cat'  => 'परिवार/बसोबास',
      'cat_en' => 'Family',
      'icon' => 'heart',
      'color' => '#f5365c',
      'stay' => '२ वर्ष (नवीकरणयोग्य, स्थायी बसोबासमा रूपान्तरण सम्भव)',
      'link' => 'https://www.hikorea.go.kr',
    ],
  ];

  $categories = [];
  foreach ($visas as $v) {
    $categories[$v['cat']] = $v['cat_en'];
  }

  $smarty->assign('visas', $visas);
  $smarty->assign('visa_categories', $categories);

  page_header(__("कोरिया भिसा केन्द्र") . ' | ' . __($system['system_title']));

} catch (Exception $e) {
  _error(__("Error"), $e->getMessage());
}

page_footer('korea-visa');
