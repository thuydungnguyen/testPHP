$( document ).ready(function() {
    var module = (function(){
        var self = {};

        self.text = $('#calc_occurence').text().split(' ');
        self.tableOfOccurence = {};

        self.calcOccurence = function() {
            $.each(self.text, function(index, item) {
                if(item.length >= 2) {
                    if(self.tableOfOccurence[item] == null){
                        self.tableOfOccurence[item] = 1;
                    }else{
                        self.tableOfOccurence[item] += 1;
                    }
                }
            });
            console.log(self.tableOfOccurence);
        };

        return self;
    })();

    module.calcOccurence();
});