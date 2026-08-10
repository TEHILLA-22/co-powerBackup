<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;

/**
 * Keyword-driven product taxonomy (parent/child categories).
 *
 * The SIAN price list provides no category/brand columns, so products are
 * classified from their names/descriptions using weighted keyword rules.
 * Longer/more specific keywords score higher so overlapping terms resolve to
 * the most specific category (e.g. "fabric conditioner" beats "conditioner").
 */
class ProductTaxonomy
{
    /**
     * Full taxonomy tree. keywords are matched against a normalized
     * (lowercased, punctuation -> spaces) product name + description string.
     * Children are evaluated in order; the highest-scoring child wins.
     */
    public function taxonomy(): array
    {
        return [
            'baby-care' => $this->babyCare(),
            'hair-products' => $this->hairCare(),
            'cosmetics' => $this->cosmetics(),
            'oral-hygiene' => $this->oralCare(),
            'skin-care' => $this->skinCare(),
            'feminine-care' => $this->feminineCare(),
            'deodorants' => $this->deodorants(),
            'mens-grooming' => $this->mensGrooming(),
            'fragrance' => $this->fragrance(),
            'bath-body' => $this->bathBody(),
            'health-remedies' => $this->healthRemedies(),
            'vitamins-supplements' => $this->vitamins(),
            'home-care' => $this->homeCare(),
            'household-essentials' => $this->householdEssentials(),
            'food-drink' => $this->foodDrink(),
            'snacks-confectionery' => $this->snacks(),
            'pet-care' => $this->petCare(),
            'smoking-accessories' => $this->smokingAccessories(),
            'shop-other' => $this->other(),
        ];
    }

    protected function babyCare(): array
    {
        return [
            'name' => 'Baby Care',
            'icon' => 'fas fa-baby',
            'children' => [
                'baby-milk-formula' => [
                    'name' => 'Baby Milk & Formula',
                    'icon' => 'fas fa-bottle-droplet',
                    'keywords' => ['aptamil', 'cow gate', 'cow & gate', 'sma', 'hipp organic', 'hipp ', 'growing up milk', 'follow on milk', 'first infant', 'stage 1', 'stage 2', 'stage 3', 'infant milk', 'infacare'],
                ],
                'baby-food' => [
                    'name' => 'Baby Food & Feeding',
                    'icon' => 'fas fa-utensils',
                    'keywords' => ['baby food', 'baby rice', 'puree pouch', 'baby porridge', 'follow on baby', 'from birth', '4-6 months', '6-12 months', '7-12 months', '1 years', 'toddler', 'baby cereal', 'baby drinks'],
                ],
                'nappies-pants' => [
                    'name' => 'Nappies & Nappy Pants',
                    'icon' => 'fas fa-child',
                    'keywords' => ['nappy', 'nappies', 'baby dry', 'pampers', 'pull ups', 'pull-ups', 'pants size', 'diaper', 'huggies', 'little swimmers', 'swim pants', 'swimpants', 'starter box', 'nappy bags', 'nappy sacks', 'swim nappies', 'baby dry night'],
                ],
                'baby-wipes-toiletries' => [
                    'name' => 'Baby Wipes & Toiletries',
                    'icon' => 'fas fa-hand-sparkles',
                    'keywords' => ['baby wipes', 'baby wash', 'baby lotion', 'baby shampoo', 'baby oil', 'baby powder', 'nappy cream', 'barrier cream', 'diaper', 'baby roll', 'johnsons baby', 'johnson s baby', "johnson's baby", 'sudocrem', 'sebamed', 'baby bubble', 'baby bath', 'baby soap', 'baby moistur', 'infacare', 'dentinox', 'baby toothpaste', 'toddler toothpaste', 'baby vaginal', 'pampers wipes', 'pampers sensitive', 'pampers aqua', 'diaper rash', 'water wipes', 'care wipes', 'metanium', 'nappy ointment', 'barrier oint', 'bepanthen', 'bepanthenol'],
                ],
                'baby-accessories' => [
                    'name' => 'Baby Accessories',
                    'icon' => 'fas fa-puzzle-piece',
                    'keywords' => ['bottle', 'teat', 'breast pump', 'steriliser', 'sterilizer', 'sterilising', 'sterilizing', 'bib', 'dummy', 'soother', 'sippy cup', 'free flow cup', 'spout cup', 'feeding bottle', 'baby cup', 'baby brush', 'baby comb', 'cooling gel', 'teether', 'rattle', 'baby monitor', 'bonjela', 'teething', 'milton', 'sterilising fluid', 'sterilising tablets', 'sterilising tabs'],
                ],
            ],
        ];
    }

    protected function hairCare(): array
    {
        return [
            'name' => 'Hair Care',
            'icon' => 'fas fa-user',
            'children' => [
                'shampoo' => [
                    'name' => 'Shampoo',
                    'icon' => 'fas fa-shampoo',
                    'keywords' => ['shampoo', 'shampoing', '2 in 1 shampoo', '2in1 shampoo', 'clarifying wash', 'scalp cleanser', 'anti dandruff', 'dandruff shampoo', 'head lice shampoo', 'colour shampoo', 'cantu shampoo', 'cantu cream shampoo', 'sulphate free shampoo', 'cocont shampoo', 'shamp', '2in1', '2 in 1', 'head and shoulders', 'head & shoulders', 'h and s', 'itchy', 'alpecin', 'caffeine liquid', 'anti hair loss', 'hair loss', 'anti-dandruff', 'dandruff', 'itchy scalp'],
                ],
                'conditioner' => [
                    'name' => 'Conditioner',
                    'icon' => 'fas fa-water',
                    'keywords' => ['conditioner', 'conditioning', 'hair rinse', 'detangling', 'leave in conditioner', 'leave-in conditioner', 'condit', 'rinse condit', 'detangler', 'cantu', 'cond', 'hair food', 'ub hair food', 'hair masque', 'masque', 'sleek restorer', 'mielle', 'rosemary mint', 'food cond', 'food shampoo'],
                ],
                'styling-treatment' => [
                    'name' => 'Styling & Treatments',
                    'icon' => 'fas fa-wind',
                    'keywords' => ['hairspray', 'hair spray', 'mousse', 'hair gel', 'styling', 'hair wax', 'pomade', 'salt spray', 'beach matte', 'root lift', 'root boost', 'volume spray', 'thickening', 'hair mask', 'hair oil', 'argan oil', 'coconut oil', 'leave in', 'leave-in', 'hair tonic', 'scalp tonic', 'hair treatment', 'keratin', 'hair repair', 'hair serum', 'collagen hair', 'biotin', 'hair marvel', 'olaplex', 'heat protect', 'hair shine', 'smoothing', 'hair remedy', 'hair masque', 'sty gel', 'styling gel', 'studio line', 'studio', 'spritz', 'fixing paste', 'fibre cream', 'matte & messy', 'matte clay', 'indestructible', 'out of bed', 'wire fixture', 'wella', 'shockwaves', 'dax', 'pomade dax', 'wave and groom', 'short and neat', 'remix', 'got2b', 'elastic', 'cantu shea',
                        'elvive', 'elnett', 'fructis', 'aussie', 'got2b', 'tigi', 'head funk', 'silvikrin', 'vo5', 'plantur', 'ogx', 'trezzor', 'tresemme', 'pantene', 'hershesons', 'beehive', 'african pride', 'global goddess', 'unevenment', 'barber club', 'cantu', 'cantu cream', 'curl', 'curling cream', 'edge control', 'edge gel', 'refresher'],
                ],
                'hair-colour' => [
                    'name' => 'Hair Colour',
                    'icon' => 'fas fa-palette',
                    'keywords' => ['hair colour', 'hair color', 'home colour', 'home color', 'permanent colour', 'permanent color', 'semi permanent', 'hair dye', 'colouring', 'coloring', ' hair tint', 'root touch', 'root retouch', 'olia', 'nutrisse', 'preference', 'castings', 'garnier colour', 'schwarzkopf', 'live colour', 'excellence', 'loreal colour', 'loreal color', 'color sensation', 'colour sensation', 'garnier color', 'retouch', 'auburn', 'mochacchino', 'hibiscus brown', 'golden blonde', 'diamond blonde', 'icy chestnut', 'intense ruby', 'onyx black'],
                ],
                'head-lice' => [
                    'name' => 'Head Lice & Nits',
                    'icon' => 'fas fa-bug',
                    'keywords' => ['lice', 'nits', 'head lice', 'nit comb', 'tesco lice', 'detection comb', 'lice repellent'],
                ],
            ],
        ];
    }

    protected function cosmetics(): array
    {
        return [
            'name' => 'Cosmetics & Makeup',
            'icon' => 'fas fa-wand-magic-sparkles',
            'children' => [
                'makeup' => [
                    'name' => 'Makeup',
                    'icon' => 'fas fa-makeup-brush',
                    'keywords' => ['mascara', 'foundation', 'lipstick', 'eyeliner', 'eyeshadow', 'eye shadow', 'blush', 'concealer', 'makeup', 'make up', 'make-up', 'falsies', 'bb cream', 'bbcream', 'toning cream', 'cc cream', 'primer', 'highlighter', 'bronzer', 'multi stick', 'lip crayon', 'lip gloss', 'lip liner', 'powder compact', 'eyebrow', 'brows & edge', 'tint brow', 'lip oil', 'lip tint', 'contour', 'setting spray', 'maybelline', 'l oreal paris lip', 'revlon', 'rimmel', 'barry m', 'b perfect', 'collection 2000', 'elizabeth arden', 'bourjois', 'essence makeup', 'garnier bb', 'garnier classic', 'makeup remover wipes', 'make-up remover wipes', 'remover wipes', 'm up rmvr', 'm/up rmvr', 'rmvr', 'make up remover', 'eye shapers', 'eyebrow shapers', 'falsies lashes'],
                ],
                'nails' => [
                    'name' => 'Nail Care',
                    'icon' => 'fas fa-hand',
                    'keywords' => ['nail polish', 'nail varnish', 'nail lacquer', 'essie', 'gellux', 'manicure', 'cuticle', 'nail treatment', 'base coat', 'top coat', 'gel nails', 'press on nails', 'false nails', 'nail strips', 'sinful colour', 'collection nail', 'nail polish remover', 'nail varnish remover', 'nail remover', 'cutex', 'npr', 'acetone'],
                ],
                'face-tools' => [
                    'name' => 'Skincare Tools & Applicators',
                    'icon' => 'fas fa-toolbox',
                    'keywords' => ['cotton pads', 'cosmetic pads', 'cotton wool', 'cotton roll', 'cotton buds', 'qtips', 'q tips', 'facial roller', 'gua sha', 'makeup sponge', 'beauty blender', 'brush cleaner makeup', 'makeup brush', 'lash curler', 'pore strips', 'nose strips', 'blackhead', 'facial brush', 'silk pads', 'nano mist', 'face mask brush', 'cotton pleat', 'cotton ', 'pleat', 'puff', 'make up puff', 'compact puff'],
                ],
            ],
        ];
    }

    protected function oralCare(): array
    {
        return [
            'name' => 'Oral Care',
            'icon' => 'fas fa-tooth',
            'children' => [
                'toothpaste' => [
                    'name' => 'Toothpaste',
                    'icon' => 'fas fa-tooth',
                    'keywords' => ['toothpaste', 'tooth paste', 't paste', 'tpaste', 'dental cream', 'dentrifice', 'whitening toothpaste', 'sensitive toothpaste', 'smokers toothpaste', 'chlorhexidine gel', 'teeth whitening kit', 'glister', 'tongue gel', 'dental gel', 'tooth powder', 't powder', 'eucryl', 'dental powder'],
                ],
                'toothbrushes' => [
                    'name' => 'Toothbrushes & Brush Heads',
                    'icon' => 'fas fa-brush',
                    'keywords' => ['toothbrush', 'tooth brush', 't brush', 'tbrush', 'brush heads', 'brushhead', 'refill heads', 'spinbrush', 'tongue brush', 'tongue scraper', 'electric toothbrush', 'oralb', 'vitality', 'pro twin', 'pro-twin', 'pro clean', 'pro1', 'io series', 'smart series', 'electric brush', 'wisdom', 'xtra clean', 'tung', 'triple', 'fresh firm', 'fresh medium', 'regular firm'],
                ],
                'floss-interdental' => [
                    'name' => 'Floss & Interdental',
                    'icon' => 'fas fa-dental-floss',
                    'keywords' => ['floss', 'flossers', 'interdental', 'dentotape', 'dentotapes', 'dental tape', 'soft picks', 'tepe', 'single tuft', 'gum brushes'],
                ],
                'mouthwash' => [
                    'name' => 'Mouthwash',
                    'icon' => 'fas fa-glass-water',
                    'keywords' => ['mouthwash', 'mouth wash', 'm wash', 'mwash', 'mouth rinse', 'listerine', 'plax', 'oral rinse', 'daily rinse', 'gum mouth', 'breath freshener', 'breath spray', 'fresh breath', 'breath freshening', 'freshener', 'freshen', 'wisdom breath', 'xpel medex'],
                ],
                'denture-care' => [
                    'name' => 'Denture Care',
                    'icon' => 'fas fa-teeth',
                    'keywords' => ['denture', 'dentures', 'fixative', 'steradent', 'polident', 'dental adhesive', 'denture cleaner', 'denture tablets', 'fixodent', 'denture fixative'],
                ],
            ],
        ];
    }

    protected function skinCare(): array
    {
        return [
            'name' => 'Skin Care',
            'icon' => 'fas fa-face-smile',
            'children' => [
                'face-cleansers' => [
                    'name' => 'Cleansers & Toners',
                    'icon' => 'fas fa-droplet',
                    'keywords' => ['face wash', 'facial wash', 'cleanser', 'cleansing', 'micellar', 'toner', 'exfoliating cleanser', 'pore cleanser', 'face scrub', 'facial scrub', 'body scrub', 'cleansing water', 'face bar', 'oil cleanser', 'wash off mask peel', 'face wipes', 'facial wipes', 'makeup remover wipes', 'micellar water', 'remove wipes', 'f scrub', 'scrub', 'tonic', 'facial tonic', 'charcoal', 'deep pore', 'pore wash', 'pore water', 'vit c wash', 'vitamin c wash', 'c wash', 'wash cleanser', 'clay cleanser', 'dark spot corrector', 'spot corrector', 'corrector serum', 'hyaluronic wash', 'hylauronic wash', 'facial wash'],
                ],
                'face-care' => [
                    'name' => 'Face Care & Moisturisers',
                    'icon' => 'fas fa-face-kiss',
                    'keywords' => ['face cream', 'facial cream', 'day cream', 'night cream', 'moisturiser', 'moisturizer', 'serum', 'eye cream', 'eye serum', 'eye pads', 'eye pad', 'cooling eye', 'under eye', 'retinol', 'hyaluronic', 'hyaluron', 'face oil', 'facial oil', 'face mist', 'facial mist', 'spf cream', 'face lotion', 'facial lotion', 'vitamin c face', 'vitamin c cream', 'anti age', 'anti-age', 'anti aging', 'anti ageing', 'wrinkle', 'plump', 'rejuvenating', 'cerave', 'eucerin face', 'olay', 'simple face', 'skin academy', 'face facts', 'nuage', 'qv ', 'lubriderm', 'cetaphil face', 'epiduo', 'differin', 'adapalene', 'zit killz', 'face mask', 'facial mask', 'sheet mask', 'bubble mask', 'mask ', 'face patches', 'pimple patch', 'spot patch', 'eye patch', 'eye patches', 'eye gel patch', 'face mud', 'clay mask', 'aloe gel', 'spot cream', 'blemish', 'essence', 'emulsion', 'tissue mask', 'under eye patch', 'nivea men', 'men creme', 'men cream', 'men lotion', 'men face', 'men expert face', 'loreal', 'l oreal', 'age perfect', 'revitalift', 'hydra genius', 'liquid care', 'dermo exp', 'dermoexp', 'golden age', 'palmer', 'palmers', 'skin success', 'eucerin', 'derma v10', 'dermav10', 'aloe vera gel', 'antiredness', 'redness', 'sorbet', 'sorbet cream', 'day pot', 'night pot', 'rosy day', 'anti pigment', 'anti redness', 'anti-redness', 'aquaphor', 'atocontrol', 'eucerin urea', 'eucerin lotion', 'collagen', 'vit c brightening', 'vitamin c brightening', 'brightening', 'glass skin', 'laser', 'men expert derma', 'men expert pure', 'men expert quenching', 'men expert power age', 'anti fatigue', 'antifatigue', 'hydraener', 'hydra energetic', 'energetic wash', 'charcoal matte', 'matte cream', 'air matte', 'pure active', 'derma control', 'carbon protect', 'hydraenergetic'],
                ],
                'body-lotions' => [
                    'name' => 'Body Lotion & Creams',
                    'icon' => 'fas fa-notes-medical',
                    'keywords' => ['body lotion', 'body butter', 'body cream', 'body oil', 'body milk', 'massage cream', 'massage oil', 'body milk', 'cocoa butter', 'shea butter', 'body moisturiser', 'body moisturizer', 'firming lotion', 'tanning lotion', 'scented lotion', 'barrier cream', 'aqueous cream', 'emollient', 'ointment', 'calamine', 'dove cream', 'dove beauty', 'aveeno', 'vaseline', 'nivea', 'nivea creme', 'nivea lotion', 'nivea soft', 'e45', 'e45 cream', 'garnier body', 'body superfood', 'body repair', 'body food', 'summer body', 'body moisturising', 'body moisturizing', 'eucerin urea', 'body powder', 'talc', 'talcum', 'liquid talc', 'cooling powder', 'xpel', 'cocobutter', 'body repair hand', 'vaseline intens' , 'vaseline healing', 'vaseline healthy'],
                ],
                'hand-care' => [
                    'name' => 'Hand Care',
                    'icon' => 'fas fa-hand-peace',
                    'keywords' => ['hand cream', 'hand lotion', 'hand oil', 'hand & nail', 'hand and nail', 'nail cream', 'hand mask', 'hand repair', 'cuticle cream', 'knuckles', 'hand balm', 'hand gel', 'hand sanitiser', 'hand sanitizer', 'carex', 'hand soap', 'hand wash', 'cidal', 'handwash', 'wrights', 'handfood', 'hand food', 'body repair hand', 'garnier hand', 'handcare', 'hand creme', 'h wash', 'h/wash', 'hand wash'],
                ],
                'sun-care' => [
                    'name' => 'Sun Care',
                    'icon' => 'fas fa-sun',
                    'keywords' => ['sun', 'sunscreen', 'suncream', 'sun cream', 'sun block', 'sunblock', 'after sun', 'aftersun', 'spf', 'uv ', 'ambrel solaire', 'garnier ambre', 'tanning', 'sunbed', 'fake tan', 'self tan', 'solar', 'sun protector', 'sun milk', 'sunless', 'piz buin', 'hawaiian tropic', 'nail brite sun', 'invisible spf', 'spf50', 'spf 50', 'spf30', 'spf 30', 'spf15', 'spf 15', 'spf20', 'spf 20', 'daily uv', 'sun face', 'sun face cream', 'wonder tint', 'self-tan', 'bronzer'],
                ],
'lip-care' => [
                    'name' => 'Lip Care',
                    'icon' => 'fas fa-lips',
'keywords' => ['lip balm', 'lip care', 'chapped lips', 'lip salve', 'lip repair', 'blistex', 'vaseline lip', 'nivea lip', 'eucerin lip', 'carmex', 'labello', 'lips'],
                ],
                'foot-care' => [
                    'name' => 'Foot Care',
                    'icon' => 'fas fa-shoe-prints',
                    'keywords' => ['foot', 'feet', 'hard skin', 'rough skin', 'cracked skin', 'dry & cracked', 'dry skin cream', 'foot powder', 'foot cream', 'foot lotion', 'foot balm', 'callus', 'corn remover', 'heel ', 'pedicure', 'foot mask', 'peel mask', 'xpel', 'daktarin', 'aktiv', 'freeze gel', 'heat spray', 'corn removal', 'athletes foot', 'athlete foot'],
                ],
                'hair-removal' => [
                    'name' => 'Hair Removal',
                    'icon' => 'fas fa-razor',
                    'keywords' => ['wax ', 'waxing', 'wax strip', 'wax strips', 'depilatory', 'hair removal', 'hair remover', 'hair removing', 'veet', 'immac', 'byly', 'epilator', 'nair', 'cold wax'],
                ],
            ],
        ];
    }

    protected function feminineCare(): array
    {
        return [
            'name' => 'Feminine Care',
            'icon' => 'fas fa-venus',
            'children' => [
                'sanitary-towels' => [
                    'name' => 'Sanitary Towels & Pads',
                    'icon' => 'fas fa-bed',
                    'keywords' => ['sanitary', 'sanitory', 'towels', 'pads', 'maxi', 'ultra', 'overnight', 'night pads', 'day pads', 'secure', 'always ', 'bodyform', 'lil-lets', 'lil lets', 'tampon', 'tampax', 'playtex', 'night size', 'sanitary pads'],
                ],
                'pantyliners' => [
                    'name' => 'Pantyliners',
                    'icon' => 'fas fa-gem',
                    'keywords' => ['pantyliner', 'panty liner', 'liners', 'dailies', 'discreet', 'fresh scented', 'washable liners'],
                ],
                'intimate-care' => [
                    'name' => 'Intimate Care',
                    'icon' => 'fas fa-heart-pulse',
                    'keywords' => ['intimate', 'v wash', 'v-wash', 'feminine wash', 'vaginal', 'pessary', 'candid', 'thrush', 'cystitis', 'canesten intimacy', 'v san', 'v daily', 'femfresh', 'feminine hygiene', 'intimate wash', 'daily wash'],
                ],
                'pregnancy-fertility' => [
                    'name' => 'Pregnancy & Fertility Tests',
                    'icon' => 'fas fa-baby',
                    'keywords' => ['pregnancy test', 'pregnancy', 'ovulation test', 'ovulation', 'clearblue', 'fertility', 'first response', 'pregnancy & fertility', 'prubea', 'home test kit', 'my little miracle'],
                ],
            ],
        ];
    }

    protected function deodorants(): array
    {
        return [
            'name' => 'Deodorants & Body Sprays',
            'icon' => 'fas fa-spray-can',
            'children' => [
                'rollon-sticks' => [
                    'name' => 'Roll-Ons & Sticks',
                    'icon' => 'fas fa-circle-dot',
                    'keywords' => ['roll on', 'roll-on', 'rollon', 'deo stick', 'deodorant stick', 'stick deodorant', 'crystal stick', 'crystal deodorant', 'antiperspirant stick', 'apd stick', 'a p d stick', 'deodorant crystal'],
                ],
                'sprays-mists' => [
                    'name' => 'Sprays & Mists',
                    'icon' => 'fas fa-cloud',
                    'keywords' => ['body spray', 'body mist', 'deo spray', 'deodorant spray', 'antiperspirant', 'anti perspirant', 'anti-perspirant', 'apd', 'a p d', 'aerosol deo', 'spray deo', 'activity spray', 'invisible spray', 'sure ', 'lynx', 'axe ', 'impulse', 'nivea deo', 'nivea apd', 'mitchum', 'right guard', 'brut', 'reebok deo', 'rexona', 'dove deo', 'sanex deo', 'adidas deo', 'old spice deo', 'inbox ', 'zesty', 'deodorant', 'deo ', 'bodyspray', 'body spray', 'deospray', 'dove apa', 'men apa', 'women apa', 'apa 1', 'apa 1', 'apa 2', 'apa 2', 'apa 150', 'apa 200', 'it s time', 'ready to rumble', 'body spry', 'deo body', 'anti perspirant spray', 'deodorant body'],
                ],
            ],
        ];
    }

    protected function mensGrooming(): array
    {
        return [
            'name' => "Men's Grooming",
            'icon' => 'fas fa-face-smile-beam',
            'children' => [
                'shaving-razors' => [
                    'name' => 'Shaving & Razors',
                    'icon' => 'fas fa-scissors',
                    'keywords' => ['razor', 'razors', 'razor blade', 'blades', 'shaving', 'shave', 'shaving foam', 'shaving cream', 'shave gel', 'shave prep', 'gillette', 'mach3', 'mach 3', 'venus', 'skinguard', 'fusion', 'bic ', 'disposable razor', 'razor refill', 'trimmer', 'beard', 'beard balm', 'beard oil', 'beard wash', 'beard cream', 'beard kit', 'beard wax', 'moustache', 'moustache wax', 'kcg', 'beard shampoo', 'beard softener'],
                ],
                'aftershave' => [
                    'name' => 'Aftershave & Fragrance For Men',
                    'icon' => 'fas fa-flag',
                    'keywords' => ['after shave', 'aftershave', 'after-shave', 'old spice', 'brut ', 'inosit', 'gentl', 'swagger', 'king c gillette', 'l homre', "l'homme"],
                ],
                'shaving-brushes' => [
                    'name' => 'Shaving Accessories',
                    'icon' => 'fas fa-spa',
                    'keywords' => ['shaving brush', 'shaving bowl', 'razor stand', 'travel razor', 'on the go razor', 'gillette men', 'men pre shave', 'pre-shave', 'pre shave'],
                ],
            ],
        ];
    }

    protected function fragrance(): array
    {
        return [
            'name' => 'Fragrance',
            'icon' => 'fas fa-spa',
            'children' => [
                'perfume-cologne' => [
                    'name' => 'Perfume & Cologne',
                    'icon' => 'fas fa-spray-can-sparkles',
                    'keywords' => ['edt', 'eau de toilette', 'eau de parfum', 'cologne', 'perfume', 'parfum', 'aftershave edt', 'eden ', 'elizabeth arden', 'jennifer lopez', 'beyond paradise', 'spirit of', 'winter 100ml', 'adidas edt'],
                ],
            ],
        ];
    }

    protected function bathBody(): array
    {
        return [
            'name' => 'Bath & Body',
            'icon' => 'fas fa-bath',
            'children' => [
                'shower-gel' => [
                    'name' => 'Shower Gel & Body Wash',
                    'icon' => 'fas fa-shower',
                    'keywords' => ['shower gel', 'body wash', 'shower cream', 'shower creme', 'wash gel', 'body gel', 'body milk', 'in shower', 'shower oil', 'wash body', 'fragrance shower', 'radox', 'badedas', 'original source', 'sanex', 'dove body wash', 'dove shower', 'lux ', 'palmolive', 'imperial leather', 'matey', 'reebok shower', 'lynx shower', 'os shower', 'baylis', 'shower foam', 'whipped shower', 'body foam', 'foam shower', 'body wash foam'],
                ],
                'soap' => [
                    'name' => 'Soap & Cleansing Bars',
                    'icon' => 'fas fa-cube',
                    'keywords' => ['bar soap', 'soap ', 'cleansing bar', 'beauty bar', 'castile', 'glycerine soap', 'pears', 'liquid soap', 'body bar', 'muslin soap', 'handmade soap'],
                ],
                'bath-additives' => [
                    'name' => 'Bath Additives & Bombs',
                    'icon' => 'fas fa-bubbles',
                    'keywords' => ['bubble bath', 'bath soak', 'bath bombs', 'bath bomb', 'bath salts', 'bath oil', 'bath milk', 'bath melt', 'epsom', 'bath crystals', 'bubble mix', 'bath fizz', 'bath gel', 'radox bath', 'matey', 'cussons', 'badedas bath', 'winter bath', 'bath ', 'foam bath', 'bath foam', 'bath additive', 'carex bath', 'carex foam'],
                ],
                'body-wipes' => [
                    'name' => 'Body Wipes & Towels',
                    'icon' => 'fas fa-towel',
                    'keywords' => ['body wipes', 'shower wipes', 'body towel', 'towelling', 'flannel', 'body cloth', 'wash cloth', 'muslin towel'],
                ],
            ],
        ];
    }

    protected function healthRemedies(): array
    {
        return [
            'name' => 'Health & Remedies',
            'icon' => 'fas fa-briefcase-medical',
            'children' => [
                'pain-relief' => [
                    'name' => 'Pain Relief',
                    'icon' => 'fas fa-tablets',
                    'keywords' => ['pain', 'pain relief', 'analgesic', 'aspirin', 'paracetamol', 'ibuprofen', 'nurofen', 'anadin', 'panadol', 'naproxen', 'co-codamol', 'cocodamol', 'solpadeine', 'migraine', 'headache', 'tension', 'muscle pain', 'back ache', 'backache', 'neuralgia', 'diclofenac', 'voltarol', 'deep heat', 'deep freeze', 'pain sachet', 'granulated pain', 'calpol', 'galpharm', 'infant suspension', 'child suspension', 'suspension', 'paracetamol infant', 'paediatric'],
                ],
                'cough-cold' => [
                    'name' => 'Cough, Cold & Flu',
                    'icon' => 'fas fa-lungs',
                    'keywords' => ['cough', 'cold & flu', 'cold and flu', 'cold remedy', 'chesty', 'lozenge', 'sore throat', 'throat', 'soothers', 'lemsip', 'strepsils', 'expectorant', 'decongest', 'blocked nose', 'nasal', 'inhaler', 'olbas', 'vicks', 'halls', 'benylin', 'sudafed', 'beechams', 'cold relief', 'flu relief', 'mucus', 'phlegm', 'steam', 'menthol', 'bells cough', 'buttercup', 'snufflebabe', 'eucalyptus', 'flu ', 'galpharm', 'vapour rub', 'vapor rub', 'fishermans friend', 'jakemans', 'bells', 'loz', 'throat lozenges', 'cough sweets'],
                ],
                'allergies' => [
                    'name' => 'Allergies & Hayfever',
                    'icon' => 'fas fa-allergies',
                    'keywords' => ['allergy', 'allergies', 'hayfever', 'hay fever', 'cetirizine', 'loratadine', 'piriton', 'antihistamine', 'histamine', 'aller', 'fexofenadine', 'chlorphenamine', 'optilast', 'nasal allergy', 'hay relief'],
                ],
                'digestive' => [
                    'name' => 'Digestive & Indigestion',
                    'icon' => 'fas fa-stomach',
                    'keywords' => ['indigestion', 'reflux', 'heartburn', 'antacid', 'acid', 'constipation', 'laxative', 'diarrhoea', 'diarrhea', 'nausea', 'vomit', 'sick ', 'upset stomach', 'wind ', 'flatulence', 'gaviscon', 'pepto', 'rennies', 'setlers', 'buscopan', 'imodium', 'fybogel', 'senokot', 'lactulose', 'dulcolax', 'busbuto', 'colperm', 'bowel', 'stomach', 'diah', 'gastro', 'sickness', 'motion', 'geogamic', 'acidex', 'anusol', 'haemorrhoid', 'hemorrhoid', 'piles', 'liquid paraffin', 'paraffin', 'rehydration', 'oral rehydration', 'fluid replacement', 'milk of magnesia', 'magnesia', 'anusol ointment', 'anusol cream', 'piles ointment', 'haemorrhoid cream', 'haemorrhoid ointment', 'hemorrhoid cream'],
                ],
                'sleep-calm' => [
                    'name' => 'Sleep & Calm',
                    'icon' => 'fas fa-moon',
                    'keywords' => ['sleep', 'insomnia', 'melatonin', 'calm ', 'calming', 'night time', 'night time relief', 'lavender sleep', 'deepsleep', 'dream', 'remedy sleep', 'nurofen night', 'stress', 'anxief', 'panic', 'mood', 'relax', 'bach', 'rescue remedy', 'rescue ', 'nytol', 'bach flower', 'rescue night'],
                ],
                'firstaid' => [
                    'name' => 'First Aid & Wound Care',
                    'icon' => 'fas fa-bandage',
                    'keywords' => ['plaster', 'plasters', 'dressing', 'bandage', 'bandages', 'blister', 'wound', 'first aid', 'surgical', 'gauze', 'adhesive tape', 'micropore', 'elastoplast', 'compeed', 'second skin', 'wound care', 'plaster strip', 'waterproof plaster', 'grabbers', 'sticky first', 'nitrile', 'elbow support', 'knee support', 'wrist support', 'ankle support', 'back support', 'sports support', 'support brace', 'masterplast', 'freeze gel', 'heat spray', 'oriental balm', 'splint', 'sling', 'arm sling', 'support', 'compression'],
                ],
                'eye-ear' => [
                    'name' => 'Eye & Ear Care',
                    'icon' => 'fas fa-eye',
                    'keywords' => ['eye drops', 'eye drop', 'ear drops', 'ear wax', 'eye bath', 'eye care', 'optrex', 'hypromellose', 'artelac', 'artificial tears', 'conjunctivitis', 'stye', 'saline eye', 'eyedea', 'overear', 'eardial'],
                ],
                'skin-treatment' => [
                    'name' => 'Skin Treatments (OTC)',
                    'icon' => 'fas fa-skull-crossbones',
                    'keywords' => ['cold sore', 'zovirax', 'athlete', 'fungal', 'antifungal', 'eczema', 'psoriasis', 'dermatitis', 'acne', 'spot treatment', 'spot cream', 'spot zapper', 'zapper', 'acne patch', 'acne sticker', 'moleskin', 'verruca', 'wart', 'clearasil', 'caladryl', 'bites & stings', 'bite relief', 'sting relief', 'insect bite', 'antiseptic', 'savlon', 'detto', 'hibiscrub', 'purple care', 'peri', 'hypnose', 'medicated cream'],
                ],
                'insect-repellent' => [
                    'name' => 'Insect Repellents',
                    'icon' => 'fas fa-mosquito',
                    'keywords' => ['insect repellent', 'mosquito', 'mosquito band', 'repellent', 'autan', 'jungle formula', 'bug spray', 'smidge', 'afterbite', 'bite relief', 'sting relief', 'bite & sting', 'bites & stings', 'sting gel', 'insect bite', 'hab bite'],
                ],
                'sexual-health' => [
                    'name' => 'Sexual Health & Contraception',
                    'icon' => 'fas fa-heart-pulse',
                    'keywords' => ['condoms', 'condom', 'durex', 'lubricant', 'personal lubricant', 'lube', 'orgasm gel', 'stimulator', 'stimulating ring', 'love gel', 'extra safe', 'thin feel', 'featherlite', 'fetherlite', 'real feel', 'close fit', 'invisible condom', 'sex toy'],
                ],
            ],
        ];
    }

    protected function vitamins(): array
    {
        return [
            'name' => 'Vitamins & Supplements',
            'icon' => 'fas fa-pills',
            'children' => [
                'multivitamins' => [
                    'name' => 'Multivitamins & Daily Health',
                    'icon' => 'fas fa-pills',
                    'keywords' => ['multivitamin', 'multi vitamin', 'multivit', 'complete daily', 'daily multi', 'multigen', 'centrum', 'biotin tablets', 'berocca', 'sanatogen', 'complan', 'slim fast', 'nutritional drink', 'valupak', 'hair skin nails', 'hair nails', 'lifestyle cartons'],
                ],
                'single-vitamins' => [
                    'name' => 'Single Vitamins & Minerals',
                    'icon' => 'fas fa-capsules',
                    'keywords' => ['vitamin a', 'vitamin b', 'vitamin c tablets', 'vitamin c 1000', 'vitamin d', 'vitamin e', 'vitamin k', 'vitamin c immune', 'vitamin b12', 'vitamin b6', 'folic', 'folate', 'iron tablets', 'iron supplement', 'zinc', 'magnesium', 'calcium', 'potassium', 'selenium', 'b12', 'cod liver', 'haliborange', 'pharmacy health', 'boots vitamin', 'vitabiotics', 'wellwoman', 'wellman', 'seven seas', 'kirkland', 'key essentials', 'spatone', 'co q 10', 'coq10', 'cranberry', 'cranberry tablets', 'q10', 'iron tonic', 'liquid iron', 'vitamin c 500mg', 'valupak vitamin'],
                ],
                'omega-omega' => [
                    'name' => 'Omega, Fish Oils & Joints',
                    'icon' => 'fas fa-fish',
                    'keywords' => ['omega', 'fish oil', 'fish oils', 'krill', 'cod liver oil', 'glucosamine', 'chondroitin', 'joint care', 'joints', 'turmeric supplement', 'collagen supplement', 'hyaluronic supplement', '180mg arctic', 'promega'],
                ],
                'immunity-probiotics' => [
                    'name' => 'Immunity, Herbal & Probiotics',
                    'icon' => 'fas fa-shield-heart',
                    'keywords' => ['probiotic', 'probiotics', 'bacteria', 'immunity', 'immune', 'echinacea', 'garlic', 'ginseng', 'ginkgo', 'herbal', 'sambucol', 'propolis', 'bee ,', 'black elderberry', 'manuka', 'honey supplement', 'colostrum', 'prebiotic', 'fermented'],
                ],
                'protein-sport' => [
                    'name' => 'Sports Nutrition & Protein',
                    'icon' => 'fas fa-dumbbell',
                    'keywords' => ['protein', 'whey', 'creatine', 'bcaa', 'amino', 'pre workout', 'pre-workout', 'post workout', 'recovery shake', 'energy gel', 'isotonic', 'sport drink', 'gainer', 'mass gain', 'vitargo', 'science in sport', 'sis', 'go energy'],
                ],
                'sleep-supplement' => [
                    'name' => 'Sleep & Calm Supplements',
                    'icon' => 'fas fa-moon',
                    'keywords' => ['melatonin supplement', 'sleep tablets', 'night tablets', 'calm supplement', 'valerian','passion flower', '5-htp', 'relax supplement', 'ashwagandha', 'magnesium sleep', 'sleep 500', 'pukka night'],
                ],
            ],
        ];
    }

    protected function homeCare(): array
    {
        return [
            'name' => 'Home & Laundry Care',
            'icon' => 'fas fa-house-chimney',
            'children' => [
                'laundry' => [
                    'name' => 'Laundry & Fabric Care',
                    'icon' => 'fas fa-shirt',
                    'keywords' => ['laundry', 'washing detergent', 'washing liquid', 'detergent', 'fabric conditioner', 'fabric softener', 'washing pods', 'laundry pods', 'pods', 'wash boost', 'booster', 'stain ', 'stain remover', 'brightener', 'colour catcher', 'crease', 'aril', 'ariel', 'bold', 'surf', 'persil', 'comfort', 'daz', 'lenor', 'fairy', 'vanish', 'drum washing', 'daz washing', 'radiant', 'dirt & colour', 'clothes', 'ironing', 'starch', 'dr beckmann', 'beckmann', 'magic leaves', 'glowhite', 'colour collector', 'dirt collector', 'service it', 'travel wash', 'fabric whitener', 'fabric wash'],
                ],
                'dishwashing' => [
                    'name' => 'Dishwashing',
                    'icon' => 'fas fa-plate-wheat',
                    'keywords' => ['washing up', 'dishwasher', 'dish soap', 'dish liquid', 'dishwasher tablet', 'dishwasher salt', 'rinse aid', 'scour', 'sponge', 'dish', 'sink'],
                ],
                'surface-cleaning' => [
                    'name' => 'Cleaning & Multi-Surface',
                    'icon' => 'fas fa-broom',
                    'keywords' => ['cleaner', 'cleaning', 'trigger spray', 'multi-surface', 'multisurface', 'multi purpose', 'multipurpose', 'all purpose', 'elbow grease', 'flash', 'astonish', 'cif', 'fabulosa', 'zipper', 'step', 'bathroom cleaner', 'kitchen cleaner', 'degreaser', 'limescale', 'descale', 'descaler', 'zebra', 'sprey', 'spray bleach', 'antibacterial spray', 'anti bacterial', 'antibac', 'universal', 'window cleaner', 'glass cleaner', 'gloss cleaner', 'cream cleaner', 'stainless', 'scale', 'dettol', 'zoflora', 'disinfectant', 'milclear', 'maid easy', 'star drops', 'surface cleanser', 'surface cleaner', 'surface clean', 'surface spray', 'surface wipes', 'oven & grill', 'oven grill', 'grill power', 'grill cleaner', 'mould', 'mold', 'cillit', 'black mould', 'zazzoosh', 'zazzooush', 'xanto', 'bbq cleaner', 'fireplace cleaner', 'bio wipes', 'hygiene wipes'],
                ],
                'air-freshener' => [
                    'name' => 'Air Fresheners & Candles',
                    'icon' => 'fas fa-sun',
                    'keywords' => ['air freshener', 'air-freshener', 'autospray', 'freshmatic', 'air-o-matic', 'air o matic', 'air wick', 'glade', 'ambipur', 'ambi pur', 'candle', 'tealight', 'tea light', 'aromatherapy', 'reed diffuser', 'diffuser', 'room spray', 'air freshen', 'insette', 'febreze', 'aircarbon', 'scent box', 'air vent', 'air palm', 'air bomb'],
                ],
                'toilet-care' => [
                    'name' => 'Toilet Care',
                    'icon' => 'fas fa-toilet',
                    'keywords' => ['toilet ', 'loo ', 'wc ', 'toilet block', 'toilet cleaner', 'toilet gel', 'rim block', 'rimblock', 'bleach block', 'bleach ', 'toilet wipes', 'toilet brush', 'harpic', 'domestos', 'toilet leaves', 'toilet tabs', 'toilet rim', 'frangipani toilet', 'flushing', 'flush ', 'andrex', 'toilet tissue', 'bathroom tissue', 'naked', 'velvet toilet', 'quilted', 'viakal', 'limescale remover'],
                ],
                'kitchen-storage' => [
                    'name' => 'Kitchen & Food Storage',
                    'icon' => 'fas fa-utensils',
                    'keywords' => ['cling film', 'clingfilm', 'food wrap', 'freezer bags', 'freeze bags', 'food bags', 'sandwich bags', 'carrier bags', 'kitchen roll', 'kitchen towel', 'baking paper', 'baking parchment', 'greaseproof', 'foil ', 'aluminium foil', 'aluminum foil', 'bacofoil', 'cling ', 'wrap & slide', 'lunch bags', 'storage bag', 'zip lock', 'margarine', 'food container', 'tin foil', 'oven bag', 'rotisserie', 'refuse sack', 'refuse sacks', 'sacks', 'bin liner', 'binliner', 'bin bag', 'rubbish', 'recycling bag', 'bag clip', 'bag clips', 'snap clips', 'clip bag', 'sealapack', 'toaster bags', 'fridge & freeze', 'fridge bags', 'fruit & vegetable', 'ziplock', 'zipper bag', 'food storage'],
                ],
                'wipes-cloths' => [
                    'name' => 'Wipes & Cleaning Cloths',
                    'icon' => 'fas fa-hands',
                    'keywords' => ['cleaning wipes', 'antibacterial wipes', 'anti bacterial wipes', 'surface wipes', 'mop refill', 'mop pads', 'floor wipes', 'cloth', 'smellode', 'wipe', 'dodo', 'mega wipes', 'microfibre', 'microfiber', 'scouring', 'j cloths', 'sponges', 'spunge', 'speedy', 'dust cloths'],
                ],
            ],
        ];
    }

    protected function householdEssentials(): array
    {
        return [
            'name' => 'Household Essentials',
            'icon' => 'fas fa-layer-group',
            'children' => [
                'batteries' => [
                    'name' => 'Batteries & Chargers',
                    'icon' => 'fas fa-battery-full',
                    'keywords' => ['battery', 'batteries', 'duracell', 'energizer', 'panasonic batteries', 'charger', 'rechargeable', 'aaa batteries', 'aa batteries', 'button cell', 'hearing aid battery', 'power bank'],
                ],
                'gloves-protection' => [
                    'name' => 'Gloves & Protective',
                    'icon' => 'fas fa-mitten',
                    'keywords' => ['glove', 'gloves', 'marigold', 'protective', 'rubber glove', 'washing up gloves', 'disposable glove', 'safety glove'],
                ],
                'lighting' => [
                    'name' => 'Lighting & Electrical',
                    'icon' => 'fas fa-lightbulb',
                    'keywords' => ['lightbulb', 'light bulb', 'bulb', 'led ', 'torch', 'flashlight', 'fuse', 'plug socket', 'extension lead', 'adapter plug'],
                ],
                'pharmacy-consumables' => [
                    'name' => 'Pharmacy & Sundries',
                    'icon' => 'fas fa-basket-shopping',
                    'keywords' => ['prescription', 'dispensing', 'pill organizer', 'pill box', 'medication', 'medicine cup', 'measuring cup', 'dropper', 'skirt hanger', 'cotton ear', 'ear bud', 'nappy sack', 'hot water bottle', 'thermometer', 'pedometer', 'first aid kit', 'needle', 'syringe', 'glucose', 'blood pressure', 'sick bag'],
                ],
                'other-household' => [
                    'name' => 'Other Household',
                    'icon' => 'fas fa-box',
                    'keywords' => ['shoelaces', 'shoelace', 'laces', 'pegs', 'clothes pegs', 'hanger', 'hangers', 'shoe polish', 'suede & nubuck', 'nubuck brush', 'valet', 'boot polish', 'carpet', 'watchstrap', 'watch strap', 'sunglasses', 'reading glasses', 'blu tack', 'pritt', 'glue stick', 'adhesive stick', 'office', 'stationery', 'rubber band', 'tape dispenser', 'stapler', 'drawing pins', 'insoles', 'insole', 'shoe brush', 'shoe spray', 'shoe care', 'shoe cover', 'garden', 'green garden', 'super glue', 'bostik', 'glue', 'kleenex', 'tissues', 'tissue', 'hankies', 'pocket pack', 'facial tissues', 'swirl', 'lint roller', 'dryer balls', 'waste bag', 'garden bag'],
                ],
            ],
        ];
    }

    protected function foodDrink(): array
    {
        return [
            'name' => 'Food & Drink',
            'icon' => 'fas fa-burger',
            'children' => [
                'drinks' => [
                    'name' => 'Drinks & Beverages',
                    'icon' => 'fas fa-bottle-water',
                    'keywords' => ['drink', 'water ', 'still water', 'spring water', 'juice', 'squash', 'coffee', 'tea', 'hot chocolate', 'chocolate drink', 'smoothie', 'energy drink', 'pepsi', 'coca cola', 'coke', 'fanta', 'sprite', 'lucozade', 'red bull', 'osta', 'fruit shoot', 'carrier', 'vintage', 'mineral water', 'cranberry juice', 'apple juice', 'orange juice', 'ice tea', 'iced tea', 'slim line', 'cloudy', 'kenco', 'decaf', 'instant coffee', 'ground coffee', 'kettle', 'cafetiere'],
                ],
                'cooking' => [
                    'name' => 'Cooking & Condiments',
                    'icon' => 'fas fa-seasoning',
                    'keywords' => ['sauce', 'ketchup', 'stock cube', 'stock pot', 'gravy', 'marmite', 'mayonnaise', 'mustard', 'vinegar', 'oil ', 'olive oil', 'cooking oil', 'frylight', 'salt ', 'pepper', 'seasoning', 'herb', 'spice', 'chopped tomato', 'passata', 'pasta', 'pasta sauce', 'baking ', 'flour', 'sugar', 'cornflour', 'rice ', 'noodles', 'cereal', 'porridge', 'oats', 'weetabix', 'shreddies', 'cornflakes', 'corn flakes', 'frosties', 'kelloggs', 'kellogg s', 'kelloggs', 'special k', 'bran flakes', 'coco pops', 'granola', 'muesli', 'jam', 'honey', 'spread', 'peanut butter', 'maple', 'custard', 'custard powder', 'jelly', 'trifle', 'angel delight', 'raisins', 'dates', 'sultana', 'mixed fruit', 'popcorn', 'pop ', 'soup', 'stock', 'sweetener', 'canderel', 'steria', 'glucose syrup', 'sugar free', 'zazzus', 'air fryer', 'fryer liner', 'air fryer liner'],
                ],
            ],
        ];
    }

    protected function snacks(): array
    {
        return [
            'name' => 'Snacks & Confectionery',
            'icon' => 'fas fa-candy-cane',
            'children' => [
                'chocolate' => [
                    'name' => 'Chocolate & Bars',
                    'icon' => 'fas fa-chocolate',
                    'keywords' => ['chocolate', 'choc', 'chocolate bar', 'milk bar', 'bar s', 'cadbury', 'mars ', 'twix', 'snickers', 'milky way', 'galaxy', 'kit kat', 'kitkat', 'aero', 'boost', 'wispa', 'flake', 'daim', 'bounty', 'creme egg', 'caramel', 'lid chocol', 'maltesers', 'skittles', 'm&m', 'celebrations', 'heroes', 'quality street', 'bueno', 'nutella', 'sweet ', 'chocolate pack', 'fmcg luxury', 'lion', 'yorkie', 'milkybar', 'milky bar'],
                ],
                'crisps-snacks' => [
                    'name' => 'Crisps & Savoury Snacks',
                    'icon' => 'fas fa-chips',
                    'keywords' => ['crisp', 'chips', 'pringles', 'walkers', 'doritos', 'monster munch', 'wotsits', 'bugles', 'nacho', 'tortilla', 'grab bag', 'grabbag', 'share bag', 'pretzel', 'pork scratch', 'nuts ', 'peanuts', 'popcorn ', 'french fries', 'fries '],
                ],
                'sweets-gum' => [
                    'name' => 'Sweets & Chewing Gum',
                    'icon' => 'fas fa-candy',
                    'keywords' => ['sweet', 'sweets', 'chewing gum', 'gum ', 'chewit', 'haribo', 'maynards', 'bassett', 'gummy', 'jelly sweets', 'sour ', 'sherbet', 'toffee', 'fudge', 'mints', 'refreshers', 'fruit gums', 'wine gums', 'chupa chups sweets', 'lollies', 'lollipop', 'drumstick', 'swizzels', 'skittles sweets', 'fizzers', 'munchies', 'rolo', 'rowntrees', 'fruit pastilles', 'pastilles', 'polo', 'polo mints', 'fruit gums'],
                ],
                'biscuits' => [
                    'name' => 'Biscuits & Cakes',
                    'icon' => 'fas fa-cookie',
                    'keywords' => ['biscuit', 'biscuits', 'cookie', 'cookies', 'mcvities', 'digestive', 'hobnob', 'rich tea', 'shortbread', 'fig roll', 'jaffa', 'bourbon', 'custard cream', 'wafer', 'wagon wheel', 'tunnock', 'cracker', 'gingerbread', 'macaron', 'brownie', 'crispy cake', 'fairy cake', 'pound cake', 'cake bar', 'fudge cake', 'sponge cake'],
                ],
                'pot-noodle' => [
                    'name' => 'Pot Noodles & Meals',
                    'icon' => 'fas fa-bowl-food',
                    'keywords' => ['pot noodle', 'pots ', 'instant noodles', 'noodle pot', 'super noodles', 'batchelors', 'cup soup', 'pasta pot', 'pot rice', 'pouch meal'],
                ],
            ],
        ];
    }

    protected function petCare(): array
    {
        return [
            'name' => 'Pet Care',
            'icon' => 'fas fa-paw',
            'children' => [
                'pet-food-treats' => [
                    'name' => 'Pet Food & Treats',
                    'icon' => 'fas fa-bone',
                    'keywords' => ['dog ', 'cat ', 'pet ', 'kitten', 'puppy', 'dog food', 'cat food', 'pet food', 'pet treat', 'dog treat', 'cat treat', 'munch & crunch', 'beggin', 'delkye', 'butchers', 'whiskas', 'felix', 'pedigree', 'bakers', 'gourmet', 'sheba', 'dreamies', 'bonio', 'gravy bones', 'meaty', 'juicy ', 'fur', 'rabbit', 'hamster', 'treat', 'cage bird'],
                ],
                'pet-toiletries' => [
                    'name' => 'Pet Toiletries & Health',
                    'icon' => 'fas fa-heart-pulse',
                    'keywords' => ['pet shampoo', 'pet lotion', 'pet jelly', 'flea ', 'worming', 'pet powder', 'dog shampoo', 'cat shampoo', 'pet wash', 'pet wipes', 'pet hygiene'],
                ],
            ],
        ];
    }

    protected function smokingAccessories(): array
    {
        return [
            'name' => 'Smoking Accessories',
            'icon' => 'fas fa-fire',
            'children' => [
                'rolling-papers' => [
                    'name' => 'Rolling Papers & Tips',
                    'icon' => 'fas fa-notes-medical',
                    'keywords' => ['rolling', 'tipping', 'king size slim', 'king size', 'swan ', 'rizla', 'ririo', 'bambu', 'ocb ', 'raw ', 'single wide', 'wide tips', 'preforated', 'perforated', 'rolling box', 'ups ', 'uk market', 'silver king', 'zig zag', 'zig-zag'],
                ],
                'tobacco-accessories' => [
                    'name' => 'Tobacco & Filter Accessories',
                    'icon' => 'fas fa-fire-flame-curved',
                    'keywords' => ['filter tips', 'filters', 'cigarette', 'smoking', 'tobacco', 'snuff', 'cigarette paper', 'cigar', 'vape', 'combust', 'zanzari', 'zippo', 'lighter fuel', 'lighter', 'butane', 'flints', 'lighter refill'],
                ],
            ],
        ];
    }

    protected function other(): array
    {
        return [
            'name' => 'Shop & Other',
            'icon' => 'fas fa-tags',
            'children' => [
                'other-merchandise' => [
                    'name' => 'Other Merchandise',
                    'icon' => 'fas fa-shapes',
                    'keywords' => ['shoelace', 'lens cleaner', 'lens wipes', 'lens cleaning', 'lens cleaning wipes', 'reading glass', 'valet', 'suede', 'nubuck', 'heading card', 'gift set', 'reusable bag', 'string', 'velcro', 'hook & loop', 'emergency', 'multitool', 'keyring', 'lanyard', 'use carm', 'carm12', 'carm15', 'carm 1', 'carm'],
                ],
            ],
        ];
    }

    /**
     * Normalize text for keyword matching (lowercase, punctuation -> spaces).
     */
    public function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = str_replace('&', ' and ', $text);
        $text = preg_replace('/[^a-z0-9]+/u', ' ', $text) ?? $text;
        return ' ' . trim($text) . ' ';
    }

    /**
     * Score a normalized text against a keyword list.
     * Short keywords (<=2 chars) require a word boundary; longer keywords match
     * as substrings so plurals/derivatives ("chewit" -> "chewits") still hit.
     */
    protected function scoreKeywords(string $normalized, array $keywords): int
    {
        $score = 0;
        foreach ($keywords as $keyword) {
            $kw = trim(preg_replace('/\s+/u', ' ', $this->normalize($keyword)));
            $hit = mb_strlen($kw) <= 2
                ? str_contains($normalized, ' ' . $kw . ' ')
                : str_contains($normalized, $kw);
            if ($hit) {
                $score += max(1, mb_strlen($kw));
            }
        }
        return $score;
    }

    /**
     * Classify a raw text into [parent_slug, child_slug] or null.
     */
    public function classify(string $text): ?array
    {
        $normalized = $this->normalize($text);
        $bestScore = 0;
        $best = null;

        foreach ($this->taxonomy() as $parentSlug => $parent) {
            foreach ($parent['children'] as $childSlug => $child) {
                $score = $this->scoreKeywords($normalized, $child['keywords']);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = [$parentSlug, $childSlug];
                }
            }
        }

        return $bestScore > 0 ? $best : null;
    }
}