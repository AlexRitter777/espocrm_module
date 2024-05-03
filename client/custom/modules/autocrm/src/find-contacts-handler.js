define('autocrm:find-contacts-handler', ['action-handler'], (Dep) => {

    return class extends Dep {

        initTest() {}


        findContacts(data, e) {
            Espo.Ajax.getRequest('find-contacts',{id: this.view.model.id})
                .then(response => {
                    console.log(response);

                    //create simple text list of contacts or error message
                    let message = '';
                    if(Array.isArray(response)) {
                        response.forEach((name) => {
                            message += `${name}\n\n`
                        })
                    }

                    //create simple modal window
                    this.view.createView('dialog', 'views/modal', {
                        templateContent: '<p>{{complexText viewObject.options.message}}</p>',
                        headerText: 'Clients',
                        backdrop: true,
                        message: message,
                        buttonList: [

                            {
                                name: 'close',
                                label: this.view.translate('Close'),
                            }
                        ],
                    }, view => {
                        view.render();
                    });
                }).catch(error => {
                    console.log(error.response);
            });
        }


        isTestVisible() {
            return !['Converted', 'Dead', 'Recycled'].includes(this.view.model.get('status'));
        }
    }
});