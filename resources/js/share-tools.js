export function createShareTools(config) {
    return {
        fullUrl: config.fullUrl,
        shortUrl: config.shortUrl,
        shortLinkRoute: config.shortLinkRoute,
        csrf: config.csrf,
        i18n: config.i18n,
        showQr: false,
        creatingShort: false,
        generatingQr: false,
        qrTargetUrl: '',
        qrModule: null,

        copyText(text) {
            const done = () => alert(this.i18n.copied);

            if (navigator.clipboard?.writeText) {
                return navigator.clipboard.writeText(text).then(done).catch(() => {
                    this.fallbackCopy(text);
                    done();
                });
            }

            this.fallbackCopy(text);
            done();
        },

        fallbackCopy(text) {
            const input = document.createElement('textarea');
            input.value = text;
            input.setAttribute('readonly', '');
            input.style.position = 'absolute';
            input.style.left = '-9999px';
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
        },

        async createShortLink() {
            this.creatingShort = true;
            try {
                const res = await fetch(this.shortLinkRoute, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (!res.ok) throw new Error('short link failed');
                this.shortUrl = data.short_url;
                if (this.showQr) {
                    await this.$nextTick?.();
                    await this.renderQr();
                }
                return this.shortUrl;
            } catch (e) {
                alert(this.i18n.shortError);
                throw e;
            } finally {
                this.creatingShort = false;
            }
        },

        qrUrl(preferShort = true) {
            if (preferShort && this.shortUrl) {
                return this.shortUrl;
            }

            return this.fullUrl;
        },

        async loadQrModule() {
            if (!this.qrModule) {
                this.qrModule = await window.loadQrCode();
            }

            return this.qrModule;
        },

        async toggleQr() {
            this.showQr = !this.showQr;
            if (this.showQr) {
                await this.$nextTick?.();
                await this.renderQr();
            }
        },

        async renderQr(preferShort = true) {
            this.generatingQr = true;
            try {
                const QRCode = await this.loadQrModule();
                this.qrTargetUrl = this.qrUrl(preferShort);
                await QRCode.toCanvas(this.$refs.qrCanvas, this.qrTargetUrl, {
                    width: 192,
                    margin: 2,
                    color: { dark: '#1e1b4b', light: '#ffffff' },
                });
            } catch (e) {
                alert(this.i18n.qrError);
                this.showQr = false;
                throw e;
            } finally {
                this.generatingQr = false;
            }
        },

        downloadQr() {
            const canvas = this.$refs?.qrCanvas;
            if (!canvas) return;
            const link = document.createElement('a');
            link.download = 'form-qr.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        },

        async downloadQrFile(filename = 'form-qr.png', preferShort = true) {
            if (preferShort && !this.shortUrl) {
                await this.createShortLink();
            }

            const QRCode = await this.loadQrModule();
            const url = this.qrUrl(preferShort);
            const dataUrl = await QRCode.toDataURL(url, {
                width: 512,
                margin: 2,
                color: { dark: '#1e1b4b', light: '#ffffff' },
            });
            const link = document.createElement('a');
            link.download = filename;
            link.href = dataUrl;
            link.click();
        },
    };
}

export function formShareModal(i18n) {
    return {
        show: false,
        busy: false,
        formTitle: '',
        tools: null,
        i18n,

        openShare(detail) {
            this.formTitle = detail.title;
            this.tools = createShareTools({
                fullUrl: detail.fullUrl,
                shortUrl: detail.shortUrl,
                shortLinkRoute: detail.shortLinkRoute,
                csrf: document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                i18n: this.i18n,
            });
            this.show = true;
        },

        close() {
            this.show = false;
        },

        async chooseFull() {
            if (!this.tools || this.busy) return;
            this.busy = true;
            try {
                await this.tools.copyText(this.tools.fullUrl);
                this.close();
            } finally {
                this.busy = false;
            }
        },

        async chooseShort() {
            if (!this.tools || this.busy) return;
            this.busy = true;
            try {
                if (!this.tools.shortUrl) {
                    await this.tools.createShortLink();
                }
                if (this.tools.shortUrl) {
                    await this.tools.copyText(this.tools.shortUrl);
                }
                this.close();
            } catch (e) {
                // createShortLink already alerts
            } finally {
                this.busy = false;
            }
        },

        async chooseQrFull() {
            if (!this.tools || this.busy) return;
            this.busy = true;
            try {
                await this.tools.downloadQrFile('form-qr.png', false);
                this.close();
            } catch (e) {
                alert(this.i18n.qrError);
            } finally {
                this.busy = false;
            }
        },

        async chooseQrShort() {
            if (!this.tools || this.busy) return;
            this.busy = true;
            try {
                await this.tools.downloadQrFile('form-qr.png', true);
                this.close();
            } catch (e) {
                alert(this.i18n.qrError);
            } finally {
                this.busy = false;
            }
        },
    };
}
