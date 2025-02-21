# To learn more about how to use Nix to configure your environment
# see: https://developers.google.com/idx/guides/customize-idx-env
{ pkgs, ... }: {
  # Which nixpkgs channel to use.
  channel = "stable-24.05"; # or "unstable"
  # Use https://search.nixos.org/packages to find packages
  packages = [
    (pkgs.php83.buildEnv {
       extensions = ({enabled, all}: enabled ++ (with all; [
         mongodb
       ]));
     })
    pkgs.php83Packages.composer
    pkgs.nodejs_20
    pkgs.mongodb-6_0
    pkgs.mongosh
    pkgs.nginx
  ];

  services.mongodb = {
    enable = true;
  };

  
  
  # Sets environment variables in the workspace
  env = {};
  idx = {
    # Search for the extensions you want on https://open-vsx.org/ and use "publisher.id"
    extensions = [
      "rangav.vscode-thunder-client"
    ];
    workspace = {
      onCreate = {
        # Open editors for the following files by default, if they exist:
        default.openFiles = ["index.php"];
      };
      # Runs when a workspace is (re)started
      onStart= {
          start-database = "mongod --port 27017 --fork --logpath ./.idx/database.log --dbpath ./.idx/.data";
          run-server = "cd app/Http/Handlers && php -S localhost:3000";
      };
    };
  };
}