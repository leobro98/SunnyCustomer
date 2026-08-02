export default class Customer {

    constructor(json) {

        this.id = json.id;
        this.firstName = json.first_name;
        this.lastName = json.last_name;
        this.birthDate = json.birth_date;
        this.userName = json.user_name;
    }
}
