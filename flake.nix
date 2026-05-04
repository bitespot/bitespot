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
        php = pkgs.php84;
        phpPackages = pkgs.php84Packages;
      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = [
            # Backend
            php
            phpPackages.composer

            # Frontend (Tailwind/Vite/JS)
            pkgs.nodejs_25

            # Essential utilities for Composer and general dev
            pkgs.git
            pkgs.unzip
            pkgs.zip
            pkgs.mysql84
            pkgs.cacert
          ];

          # This script runs automatically when you enter the direnv shell
          shellHook = ''
            # Provide default SSL paths so OpenSSL stops complaining
            export SSL_CERT_FILE="${pkgs.cacert}/etc/ssl/certs/ca-bundle.crt"
            export NIX_SSL_CERT_FILE="${pkgs.cacert}/etc/ssl/certs/ca-bundle.crt"
            
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