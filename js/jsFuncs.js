
//create an array with "repetition" number of elements each equals "value" 
Array.prototype.repeat = function(value, repetition)
{
  while (repetition)
  {
    this[--repetition] = value;
  }
  return this;
}

// example usage
var result = [].repeat(0,26);


// quick sort (best case -choosing the right pivot- takes O(nlogn); worst case takes O(n^2)
// js actually has a sort() function for array so use it instead
function qsort(a) {
    if (a.length == 0) return [];
 
    var left = [], right = [], pivot = a[0];
 
    for (var i = 1; i < a.length; i++) {
        a[i] < pivot ? left.push(a[i]) : right.push(a[i]);
    }
 
 	//recursively qsort the left and right side and merge them
    return qsort(left).concat(pivot, qsort(right));
}


// merge sort
var a = [34, 203, 3, 746, 200, 984, 198, 764, 9];
 
function mergeSort(arr)
{
    if (arr.length < 2)
        return arr;
 
    var middle = parseInt(arr.length / 2);
    var left   = arr.slice(0, middle);
    var right  = arr.slice(middle, arr.length);
 
    return merge(mergeSort(left), mergeSort(right));
}
 
function merge(left, right)
{
    var result = [];
 
    while (left.length && right.length) {
        if (left[0] <= right[0]) {
            result.push(left.shift());
        } else {
            result.push(right.shift());
        }
    }
 
    while (left.length)
        result.push(left.shift());
 
    while (right.length)
        result.push(right.shift());
 
    return result;
}
 
console.log(mergeSort(a));
