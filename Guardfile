# This file for guard command which you can install via `gem install guard`
guard 'phpunit2', :tests_path => 'tests', :cli => '--colors' do

  # Watch tests files
  watch(%r{^.+Test\.php$})

  # Watch implement files
  watch(%r{^src/(.+)\.php}) { |m| "tests/#{m[1]}Test.php" }

end
