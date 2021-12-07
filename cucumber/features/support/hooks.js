const {Before, After, Status} = require("cucumber")
const puppeteer = require("puppeteer");

Before(async function () {
    this.browser = await puppeteer.launch({
        args: [
            '--disable-dev-shm-usage',
            '--no-sandbox',
        ],
        headless: true,
        executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
        timeout: "30000"
    });
    this.page = await this.browser.newPage();
    await this.page.setViewport({width: 1280, height: 720});
})

After(async function (testCase) {
    if (testCase.result.status === Status.FAILED) {
        const screenshot = await this.page.screenshot({encoding: 'base64', fullPage: true});
        this.attach(screenshot, 'image/png');
    }
    await this.page.close();
    await this.browser.close();
})