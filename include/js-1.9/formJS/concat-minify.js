const fs = require("fs");
const path = require("path");
const terser = require("terser");

async function minifyAndConcatFromConfig(configPath = "concat-config.json") {
  if (!fs.existsSync(configPath)) {
    console.error(`❌ Config file not found: ${configPath}`);
    return;
  }

  const config = JSON.parse(fs.readFileSync(configPath, "utf8"));
  const { input, output, terserOptions } = config;

  if (!Array.isArray(input) || input.length === 0) {
    console.error("❌ No input files provided in config.");
    return;
  }

  let bundle = "";

  for (const file of input) {
    if (!fs.existsSync(file)) {
      console.warn(`⚠️ Skipping missing file: ${file}`);
      continue;
    }

    console.log(`🔧 Minifying ${file}...`);
    const code = fs.readFileSync(file, "utf8");

    try {
      const result = await terser.minify(code, terserOptions);

      if (!result || !result.code) {
        console.error(`❌ No code output from Terser for: ${file}`);
        continue;
      }

      bundle += result.code; // 🚫 no \n added!
    } catch (err) {
      console.error(`❌ Exception during minification: ${file}`, err);
    }
  }

  fs.writeFileSync(output, bundle);
  console.log(`✅ Output written to: ${output}`);

  // Final check: should only have 1 line
  const lineCount = fs.readFileSync(output, "utf8").split("\n").length;
  console.log("📏 Final line count:", lineCount);
}

// Run it
minifyAndConcatFromConfig();
