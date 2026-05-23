const puppeteer = require('puppeteer');

(async () => {

    try {

        const browser = await puppeteer.launch({
            executablePath: 'C:\\Users\\jchug\\.cache\\puppeteer\\chrome\\win64-148.0.7778.97\\chrome-win64\\chrome.exe',
            headless: true,
            args: [
                '--disable-gpu',
                '--disable-dev-shm-usage',
                '--disable-setuid-sandbox',
                '--no-sandbox',
            ]
        });

        const page = await browser.newPage();

        await page.setContent('<h1>Hola mundo</h1>');

        await page.pdf({
            path: 'manual.pdf',
            format: 'A4'
        });

        await browser.close();

        console.log('PDF generado');

    } catch (e) {

        console.error(e);

    }

})();