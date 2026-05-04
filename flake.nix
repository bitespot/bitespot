{
  description = "Development Environment";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, nixpkgs, flake-utils }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = import nixpkgs { inherit system; };
        
        # Define the PHP version you want to use (Laravel 11 requires PHP 8.2+)
        php = pkgs.php83;
        phpPackages = pkgs.php83Packages;
      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = [
            # Backend
            php
            phpPackages.composer

            # Frontend (Tailwind/Vite/JS)
            pkgs.nodejs_22
            pkgs.nodePackages.npm

            # Essential utilities for Composer and general dev
            pkgs.git
            pkgs.unzip
            pkgs.zip
          ];

          # This script runs automatically when you enter the direnv shell
          shellHook = ''
            echo "Development Environment Loaded!"
            echo "PHP: $(php -v | head -n 1 | awk '{print $2}')"
            echo "Composer: $(composer -V | awk '{print $3}')"
            echo "Node: $(node -v)"
            echo "---------------------------------------------------"
            echo "Quick Start:"
            echo "  1. Run 'composer install' & 'npm install'"
            echo "  2. Run 'npm run dev' (in one terminal)"
            echo "  3. Run 'php artisan serve' (in another terminal)"
          '';
        };
      }
    );
}