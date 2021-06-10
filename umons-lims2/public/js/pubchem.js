class PubChem {
    constructor() {
        this.api = axios.create({
            baseURL: 'https://pubchem.ncbi.nlm.nih.gov/rest'
        });
    }



    isCasValid(cas) {
        let match_ = cas.match(/([0-9]{2,7})-([0-9]{2})-[0-9]/);
        if(!match_) {
            return false
        }
        let digits = (match_[1] + match_[2]).split('').reverse();
        let sum = 0;

        for (let i = 0; i < digits.length; i++) {
            sum += (i + 1) * parseInt(digits[i]);
        }

        return sum % 10;
    }

    async autocompleteCAS(input) {
        try {
            const resp = await this.api.get(`/autocomplete/compound/${input}`);
            return resp.data.dictionary_terms.compound;
        } catch (e) {
            console.error('PubChem call failed !')
            console.log(e);
            return [];
        }

    }


    async findProductsCid (ncas) {

        try {
            if(this.isCasValid(ncas)) {
                const resp =  await this.api.get(`/pug/compound/name/${ncas}/cids/JSON`);
                return resp.data.IdentifierList.CID[0];
            } else {
                return null;
            }


        } catch (e) {
            console.error('PubChem call failed !')
            console.log(e);
        }
    };

    async findProductName(cid) {

        try {

            const resp = await this.api.get(`/pug/compound/cid/${cid}/synonyms/JSON`);
            return resp.data.InformationList.Information[0].Synonym[0];
        } catch (e) {
            console.error('PubChem call failed !')
            console.log(e);
        }
    }

    async findProductHazards(cid) {
        try {
            const resp = await this.api.get(`/pug_view/data/compound/${cid}/JSON?heading=Chemical+Safety`);
            return resp.data.Record.Section[0].Information[0].Value.StringWithMarkup[0].Markup.map(e=>{
                let s =  e.URL.split("/");
                return {
                    symbol: e.URL,
                    code: s[s.length -1].split('.')[0],
                    text:e.Extra
                }
            });

        } catch (e) {
            if(e.response.status === 404) {
                return [];
            } else{
                console.error('PubChem call failed !')
                console.log(e);
            }

        }
    }

    async findProductProps (cids) {

        try {
            let values = Array.isArray(cids)? cids.join(): cids;

            const resp = await this.api.get(`/pug/compound/cid/${values}/property/MolecularFormula,MolecularWeight/json`);

            if(Array.isArray(cids)) {
                return resp.data.PropertyTable.Properties;
            } else {
                return resp.data.PropertyTable.Properties[0];
            }

        } catch (e) {
            console.error('PubChem call failed !')
            console.log(e);
        }

    }

    async findProductInfoByCAS(ncas) {
        try {
            const prodCid = await this.findProductsCid(ncas);
            if (prodCid == null) {
                return  null;
            }
            return await this.findProductInfoByCid(prodCid);

        } catch (e) {
            console.error('PubChem call failed !')
            console.log(e);
        }
    }

    async findProductInfoByCid(cid) {

        try {
            const prodInfo = await this.findProductProps(cid);
            prodInfo.name = await  this.findProductName(cid);
            prodInfo.hazards = await  this.findProductHazards(cid);
            prodInfo.link = `https://pubchem.ncbi.nlm.nih.gov/compound/${cid}`;

            return prodInfo;

        } catch (e) {

            console.error('PubChem call failed !')
            console.log(e);
        }
    }
}