$( document ).ready(function() {
    var module = (function(){
        var self = {};

        self.calcOccurence = function() {
            text = $('#calc_occurence').text().split(' ');
            tableOfOccurence = {};

            $.each(text, function(index, word) {
                if(word.length >= 2) {
                    if(tableOfOccurence[word] == null){
                        tableOfOccurence[word] = 1;
                    }else{
                        tableOfOccurence[word] += 1;
                    }
                }
            });

            tableLine = '';
            htmlTable = $('#occurence-table').find('tbody');

            for (var word in tableOfOccurence){
                tableLine += '<tr><td>'+ word + '</td><td>' + tableOfOccurence[word] + '</td></tr>';
            }
            htmlTable.append(tableLine);

            console.log(tableOfOccurence);
        };

        return self;
    })();

    module.calcOccurence();
});