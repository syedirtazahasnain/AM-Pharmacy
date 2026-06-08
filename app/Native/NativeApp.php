<?php

namespace App\Native;

use Native\Laravel\App as NativeApp;
use Native\Laravel\Menu\Menu;
use Native\Laravel\Menu\MenuItem;

class NativeApp extends NativeApp
{
    public function __construct()
    {
        parent::__construct();
        
        $this->name('A.M Pharmacy System')
             ->version('1.0.0')
             ->width(1280)
             ->height(720)
             ->minWidth(1024)
             ->minHeight(600);
    }
    
    public function menu(): Menu
    {
        return Menu::create()
            ->add(Menu::create('File')
                ->add(MenuItem::create('New Invoice', 'CmdOrCtrl+N', function() {
                    $this->navigate('/invoices/create');
                }))
                ->add(MenuItem::create('Products', 'CmdOrCtrl+P', function() {
                    $this->navigate('/products');
                }))
                ->add(MenuItem::create('Customers', 'CmdOrCtrl+C', function() {
                    $this->navigate('/customers');
                }))
                ->addSeparator()
                ->add(MenuItem::create('Exit', 'CmdOrCtrl+Q', function() {
                    $this->quit();
                }))
            )
            ->add(Menu::create('View')
                ->add(MenuItem::create('Reload', 'CmdOrCtrl+R', function() {
                    $this->reload();
                }))
                ->add(MenuItem::create('Toggle Dev Tools', 'F12', function() {
                    $this->toggleDevTools();
                }))
            )
            ->add(Menu::create('Help')
                ->add(MenuItem::create('About', function() {
                    $this->showAbout();
                }))
            );
    }
}