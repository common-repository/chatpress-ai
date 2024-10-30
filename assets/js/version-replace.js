const fs = require('fs-extra');
const replace = require('replace-in-file');

const pluginFiles = ['templates/*', 'src/*', 'ChatPress.php'];

const { version } = JSON.parse(fs.readFileSync('package.json'));

replace({
  files: pluginFiles,
  from: /CHATPRESS_SINCE/g,
  to: version,
});
