<?php

declare(strict_types=1);

namespace App\Foundation\Site;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;

/**
 * SiteContext — builds the shared data every public page needs (site settings,
 * header menu from the category tree, footer text). Keeps module controllers
 * small and ensures header/footer parity with the legacy site.
 */
class SiteContext
{
    private array $settings = [];
    private array $settingshow = [];

    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
        $this->load();
    }

    private function load(): void
    {
        try {
            foreach ($this->dbal->database()->select()->from('settings')->fetchAll() as $r) {
                $this->settings[$r['setting_name']] = $r['setting_value'];
            }
        } catch (\Throwable) {
        }
        try {
            $rows = $this->dbal->database()->select()->from('settingshow')->limit(1)->fetchAll();
            $j = json_decode((string) ($rows[0]['content'] ?? ''), true);
            if (is_array($j)) {
                $this->settingshow = $j;
            }
        } catch (\Throwable) {
        }
    }

    private function cartCount(): int
    {
        try {
            $session = $this->app->make(\Witals\Framework\Contracts\Session\SessionInterface::class);
            $session->start();
            $cart = $session->get('ecommerce_cart', []);
            if (!is_array($cart)) {
                return 0;
            }
            $n = 0;
            foreach ($cart as $item) {
                $n += (int) (is_array($item) ? ($item['qty'] ?? 1) : 1);
            }

            return $n;
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $dirPath = (string) ($this->settings['dir_path'] ?? '');

        return [
            'site_name'        => (string) ($this->settings['site_name'] ?? config('app.name', 'Xanhvina')),
            'site_url'         => (string) config('app.url', ''),
            'site_description' => (string) ($this->settings['site_description'] ?? ''),
            'site_keywords'    => (string) ($this->settings['site_keywords'] ?? ''),
            'langcode'         => 'vi',
            'dir_path'         => $dirPath,
            'lang_dir'         => '',
            'hotline'          => (string) ($this->settingshow['hotline'] ?? ''),
            'company_name'     => (string) ($this->settingshow['companyname'] ?? ''),
            'companyaddress'   => (string) ($this->settingshow['companyaddress'] ?? ''),
            'companyphone'     => (string) ($this->settingshow['companyphone'] ?? ''),
            'companyemail'     => (string) ($this->settingshow['companyemail'] ?? ''),
            'companywebsite'   => (string) ($this->settingshow['companywebsite'] ?? ''),
            'facebook'         => (string) ($this->settingshow['facebook'] ?? ''),
            'youtube'          => (string) ($this->settingshow['youtube'] ?? ''),
            'googleplus'       => (string) ($this->settingshow['googleplus'] ?? ''),
            'fanpage'          => (string) ($this->settingshow['fanpage'] ?? ''),
            'twitter'          => (string) ($this->settingshow['twitter'] ?? ''),
            'instagram'        => (string) ($this->settingshow['instagram'] ?? ''),
            'copyright'        => (string) ($this->staticValue('copyright', 'content') ?: 'Copyright © 2023 by Xanhvina'),
            'quantity'         => $this->cartCount(),
            'logo'             => $this->buildLogo($dirPath),
            'menubar'          => $this->buildMenubar(),
            'slideshow'        => '',
            'footername'       => (string) ($this->staticValue('footer', 'name')),
            'textfooter'       => (string) ($this->staticValue('footer', 'content')),
            'toppage'          => '',
            'header_tag'       => '',
            'bodypage'         => '',
            'canonical'        => '',
            'site_image'       => '',
        ];
    }

    private function buildLogo(string $dirPath): string
    {
        $row = null;
        try {
            $db = $this->dbal->database();
            $cats = $db->select()->from('category')
                ->where('vitri', 'LIKE', '%:logosite:%')
                ->fetchAll();
            $catIds = array_map(static fn ($c) => (int) $c['id_category'], $cats);
            if ($catIds !== []) {
                $rows = $db->select()->from('logo')
                    ->where('active', 1)
                    ->where('id_category', 'IN', $catIds)
                    ->orderBy('thu_tu', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->limit(1)
                    ->fetchAll();
                $row = $rows[0] ?? null;
            }
        } catch (\Throwable) {
            $row = null;
        }
        if ($row === null || empty($row['image'])) {
            return '';
        }

        $name = htmlspecialchars((string) ($row['name'] ?? ''), ENT_QUOTES);
        $image = htmlspecialchars((string) $row['image'], ENT_QUOTES);

        return '<a href="/"><img src="' . $image . '" alt="' . $name . '" style="max-width:100%; margin:0 auto"  /></a>';
    }

    /**
     * Build a clean, semantic nav (new design system) from the category tree.
     * Logic (vitri LIKE '%:menubar:%', parentid children) reused from legacy.
     */
    private function buildMenubar(): string
    {
        try {
            $items = $this->dbal->database()
                ->select()->from('category')
                ->where('active', 1)
                ->where('vitri', 'LIKE', '%:menubar:%')
                ->orderBy('thu_tu', 'ASC')
                ->orderBy('name', 'ASC')
                ->fetchAll();
        } catch (\Throwable) {
            return '';
        }

        $out = '<ul class="nav  hide-on-tab hide-on-mobile">';
        foreach ($items as $rs) {
            if ((int) ($rs['id_category'] ?? 0) <= 0) {
                continue;
            }
            $idc = (int) $rs['id_category'];
            $mega = (int) ($rs['mega_menu'] ?? 0);
            $imageadv = trim((string) ($rs['imageadv'] ?? ''));
            $megaStyle = $mega > 0 ? 'position: initial;' : '';
            $link = $this->menuLink((string) ($rs['url'] ?? ''));
            $target = (int) ($rs['target'] ?? 0) === 1 ? " target='_blank'" : '';

            $submenu = $this->menuChildren($idc);
            $arrow = '';
            $hasSub = '';
            if (count($submenu) > 0) {
                $arrow = '<i class="fa fa-angle-down" aria-hidden="true"></i>';
                $hasSub = $mega > 0 ? $this->renderMegaMenu($submenu) : $this->renderSubMenu($submenu);
            }

            $style = 'background:url(' . $imageadv . ') center left no-repeat; ' . $megaStyle . ' ';
            $out .= '<li class=" " style="' . htmlspecialchars($style, ENT_QUOTES) . '"> <a href="' . htmlspecialchars($link, ENT_QUOTES) . '"' . $target . ' class="">' . htmlspecialchars(strip_tags((string) $rs['name']), ENT_QUOTES) . ' ' . $arrow . '</a>'
                . $hasSub . '</li>';
        }
        $out .= '</ul>';

        return $out;
    }

    /** @param array<int, array<string, mixed>> $submenu */
    private function renderSubMenu(array $submenu): string
    {
        $out = '<ul class="submnu1">';
        foreach ($submenu as $rs1) {
            if ((int) ($rs1['id_category'] ?? 0) <= 0) {
                continue;
            }
            $t1 = (int) ($rs1['target'] ?? 0) === 1 ? " target='_blank'" : '';
            $out .= '<li class=""><a href="' . htmlspecialchars($this->menuLink((string) $rs1['url']), ENT_QUOTES) . '"' . $t1 . '>' . htmlspecialchars(strip_tags((string) $rs1['name']), ENT_QUOTES) . '</a>';
            $sub2 = $this->menuChildren((int) $rs1['id_category']);
            if (count($sub2) > 0) {
                $out .= '<ul>';
                foreach ($sub2 as $rs2) {
                    $t2 = (int) ($rs2['target'] ?? 0) === 1 ? " target='_blank'" : '';
                    $out .= '<li class=""><a href="' . htmlspecialchars($this->menuLink((string) $rs2['url']), ENT_QUOTES) . '"' . $t2 . '>' . htmlspecialchars((string) $rs2['name'], ENT_QUOTES) . '</a></li>';
                }
                $out .= '</ul>';
            }
            $out .= '</li>';
        }

        return $out . '</ul>';
    }

    /** @param array<int, array<string, mixed>> $submenu */
    private function renderMegaMenu(array $submenu): string
    {
        $out = '<div class="mega-menu"><div class="c20"></div><div style="display:inline-block; width:1000px;"><div class="flex-container flex-centered">';
        foreach ($submenu as $rs1) {
            $img = '<img src="' . htmlspecialchars((string) ($rs1['imageadv'] ?? ''), ENT_QUOTES) . '" alt="' . htmlspecialchars(strip_tags((string) ($rs1['name'] ?? '')), ENT_QUOTES) . '" style="width:100%"/>';
            $link = $this->menuLink((string) $rs1['url']);
            $out .= '<div class="cell-1-6"><div class="mega-menu-item">'
                . '<a class="hvr-grow" href="' . htmlspecialchars($link, ENT_QUOTES) . '"> ' . $img . ' </a><div class="c10"></div>'
                . '<a class="mega-menu-item-link" href="' . htmlspecialchars($link, ENT_QUOTES) . '">' . htmlspecialchars(strip_tags((string) $rs1['name']), ENT_QUOTES) . '</a>'
                . '</div></div>';
        }

        return $out . '</div></div></div>';
    }

    private function menuLink(string $url): string
    {
        if (str_starts_with($url, '//') || str_starts_with($url, 'http')) {
            return $url;
        }

        return '/' . $url;
    }

    /**
     * Read a row from the `static` table by inwhere + column.
     */
    private function staticValue(string $inwhere, string $column): string
    {
        try {
            $rows = $this->dbal->database()
                ->select()->from('static')
                ->where('inwhere', $inwhere)
                ->limit(1)
                ->fetchAll();

            return (string) ($rows[0][$column] ?? '');
        } catch (\Throwable) {
            return '';
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function menuChildren(int $idc): array
    {
        try {
            return $this->dbal->database()
                ->select()->from('category')
                ->where('active', 1)
                ->where('parentid', $idc)
                ->orderBy('thu_tu', 'ASC')
                ->orderBy('name', 'ASC')
                ->limit(100)
                ->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }

}